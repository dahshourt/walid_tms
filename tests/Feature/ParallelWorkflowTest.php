<?php

namespace Tests\Feature;

use App\Events\StatusChanged;
use App\Models\ChangeRequest;
use App\Models\ChangeRequestCustomField;
use App\Models\Group;
use App\Models\Status;
use App\Services\Workflow\ParallelWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ParallelWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $workflowService;
    protected $splitStatus;
    protected $joinStatus;
    protected $vendorGroup;
    protected $businessGroup;
    protected $changeRequest;
    protected $vendorStartStatus;
    protected $vendorEndStatus;
    protected $businessStartStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->splitStatus = Status::factory()->create([
            'status_name' => 'Pending Create Agreed Scope'
        ]);

        $this->joinStatus = Status::factory()->create([
            'status_name' => 'Pending Update Agreed Requirements'
        ]);

        // Create statuses for the first parallel path
        $this->firstPathStartStatus = Status::factory()->create([
            'status_name' => 'Pending First Path Approval'
        ]);

        $this->firstPathEndStatus = Status::factory()->create([
            'status_name' => 'First Path Completed'
        ]);

        // Create statuses for the second parallel path
        $this->secondPathStartStatus = Status::factory()->create([
            'status_name' => 'Pending Second Path Approval'
        ]);

        $this->secondPathEndStatus = Status::factory()->create([
            'status_name' => 'Second Path Completed'
        ]);

        $this->changeRequest = ChangeRequest::factory()->create([
            'status_id' => $this->splitStatus->id
        ]);

        // Create the UI/UX custom field
        $this->uiUxField = ChangeRequestCustomField::factory()->create([
            'cr_id' => $this->changeRequest->id,
            'custom_field_name' => 'ui_ux',
            'custom_field_value' => '1'
        ]);

        $this->workflowService = app(ParallelWorkflowService::class);
    }

    /** @test */
    public function it_creates_parallel_workflow()
    {
        // Act
        $workflow = $this->workflowService->initiateParallelWorkflow(
            $this->changeRequest,
            $this->splitStatus->status_name,
            $this->joinStatus->status_name
        );

        // Assert
        $this->assertDatabaseHas('parallel_workflow_tracking', [
            'cr_id' => $this->changeRequest->id,
            'split_status_id' => $this->splitStatus->id,
            'join_status_id' => $this->joinStatus->id,
            'is_completed' => false,
            'completed_workflows' => 0,
            'required_completions' => 2
        ]);

        $this->assertCount(2, $workflow->branches);

        // Verify first path branch
        $this->assertDatabaseHas('parallel_workflow_branches', [
            'workflow_tracking_id' => $workflow->id,
            'group_id' => null,
            'start_status_id' => $this->firstPathStartStatus->id,
            'end_status_id' => $this->firstPathEndStatus->id,
            'current_status_id' => $this->firstPathStartStatus->id,
            'is_completed' => false
        ]);

        // Verify second path branch
        $this->assertDatabaseHas('parallel_workflow_branches', [
            'workflow_tracking_id' => $workflow->id,
            'group_id' => null,
            'start_status_id' => $this->secondPathStartStatus->id,
            'end_status_id' => $this->secondPathEndStatus->id,
            'current_status_id' => $this->secondPathStartStatus->id,
            'is_completed' => false
        ]);
    }

    /** @test */
    public function it_handles_first_path_completion()
    {
        // Arrange
        $workflow = $this->workflowService->initiateParallelWorkflow(
            $this->changeRequest,
            $this->splitStatus->status_name,
            $this->joinStatus->status_name
        );

        $firstPathBranch = $workflow->branches()
            ->where('start_status_id', $this->firstPathStartStatus->id)
            ->first();

        // Act - Complete the first path
        $this->workflowService->handleStatusUpdate(
            $this->changeRequest,
            $this->firstPathStartStatus->id,
            $this->firstPathEndStatus->id
        );

        // Assert
        $this->assertDatabaseHas('parallel_workflow_branches', [
            'id' => $firstPathBranch->id,
            'is_completed' => true,
            'current_status_id' => $this->firstPathEndStatus->id
        ]);

        $workflow->refresh();
        $this->assertEquals(1, $workflow->completed_workflows);
        $this->assertFalse($workflow->is_completed);
    }

    /** @test */
    public function it_handles_second_path_completion()
    {
        // Arrange
        $workflow = $this->workflowService->initiateParallelWorkflow(
            $this->changeRequest,
            $this->splitStatus->status_name,
            $this->joinStatus->status_name
        );

        $secondPathBranch = $workflow->branches()
            ->where('start_status_id', $this->secondPathStartStatus->id)
            ->first();

        // Act - Complete the second path
        $this->workflowService->handleStatusUpdate(
            $this->changeRequest,
            $this->secondPathStartStatus->id,
            $this->secondPathEndStatus->id
        );

        // Assert
        $this->assertDatabaseHas('parallel_workflow_branches', [
            'id' => $secondPathBranch->id,
            'is_completed' => true,
            'current_status_id' => $this->secondPathEndStatus->id
        ]);

        $workflow->refresh();
        $this->assertEquals(1, $workflow->completed_workflows);
        $this->assertFalse($workflow->is_completed);
    }

    /** @test */
    public function it_completes_workflow_when_all_branches_are_done()
    {
        // Arrange
        $workflow = $this->workflowService->initiateParallelWorkflow(
            $this->changeRequest,
            $this->splitStatus->status_name,
            $this->joinStatus->status_name
        );

        // Complete first path
        $this->workflowService->handleStatusUpdate(
            $this->changeRequest,
            $this->firstPathStartStatus->id,
            $this->firstPathEndStatus->id
        );

        // Complete second path
        $this->workflowService->handleStatusUpdate(
            $this->changeRequest,
            $this->secondPathStartStatus->id,
            $this->secondPathEndStatus->id
        );

        // Assert
        $workflow->refresh();
        $this->assertTrue($workflow->is_completed);
        $this->assertEquals(2, $workflow->completed_workflows);

        $this->assertDatabaseHas('change_requests', [
            'id' => $this->changeRequest->id,
            'status_id' => $this->joinStatus->id
        ]);
    }

    /** @test */
    public function it_throws_exception_if_ui_ux_field_not_set()
    {
        // Arrange - Remove UI/UX custom field
        $this->changeRequest->changeRequestCustomFields()->delete();

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('UI/UX custom field is not set to 1');

        // Act
        $this->workflowService->initiateParallelWorkflow(
            $this->changeRequest,
            $this->splitStatus->status_name,
            $this->joinStatus->status_name
        );
    }

    /** @test */
    public function it_transitions_to_request_draft_cr_doc_when_ui_ux_enabled()
    {
        // Arrange - Create the Request Draft CR Doc status
        $draftCrDocStatus = Status::factory()->create([
            'status_name' => 'Request Draft CR Doc'
        ]);

        // Act - Initiate workflow with UI/UX enabled
        $workflow = $this->workflowService->initiateParallelWorkflow(
            $this->changeRequest,
            $this->splitStatus->status_name,
            $this->joinStatus->status_name
        );

        // Assert - Check if the change request status was updated to Request Draft CR Doc
        $this->changeRequest->refresh();
        $this->assertEquals($draftCrDocStatus->id, $this->changeRequest->status_id);

        // Verify the status record was created
        $this->assertDatabaseHas('change_request_statuses', [
            'cr_id' => $this->changeRequest->id,
            'old_status_id' => $this->splitStatus->id,
            'new_status_id' => $draftCrDocStatus->id,
            'active' => true
        ]);
    }

    /** @test */
    public function it_dispatches_status_changed_event()
    {
        // Arrange
        Event::fake();
        $workflow = $this->workflowService->initiateParallelWorkflow(
            $this->changeRequest,
            $this->splitStatus->status_name,
            $this->joinStatus->status_name
        );

        // Act - Complete the first path
        $this->workflowService->handleStatusUpdate(
            $this->changeRequest,
            $this->firstPathStartStatus->id,
            $this->firstPathEndStatus->id
        );

        // Assert
        Event::assertDispatched(StatusChanged::class, function ($event) {
            return $event->changeRequest->id === $this->changeRequest->id &&
                   $event->oldStatusId === $this->firstPathStartStatus->id &&
                   $event->newStatusId === $this->firstPathEndStatus->id;
        });
    }
}
