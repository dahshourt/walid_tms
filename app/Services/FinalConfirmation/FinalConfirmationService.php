<?php

namespace App\Services\FinalConfirmation;

use App\Http\Repository\FinalConfirmation\FinalConfirmationRepository;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class FinalConfirmationService
{
    private $finalConfirmationRepository;
    private $changeRequestRepository;
    private function getRejectStatusId(): int
    {
        return config('change_request.status_ids.Reject');
    }

    private function getCancelStatusId(): int
    {
        return config('change_request.status_ids.Cancel');
    }

    public function __construct(
        FinalConfirmationRepository $finalConfirmationRepository,
        ChangeRequestRepository $changeRequestRepository
    ) {
        $this->finalConfirmationRepository = $finalConfirmationRepository;
        $this->changeRequestRepository = $changeRequestRepository;
    }

    /**
     * Process final confirmation action (reject/cancel)
     *
     * @param string $crNumber
     * @param int $statusId
     * @param int $userId
     * @return array
     */
    public function processFinalConfirmation(string $crNumber, int $statusId, int $userId, string $technical_feedback): array
    {
        try {
            // Find the change request by number
            $changeRequest = $this->finalConfirmationRepository->findCRByNumber($crNumber);

            if (!$changeRequest) {
                return [
                    'success' => false,
                    'message' => "Change Request #{$crNumber} not found."
                ];
            }

            // Get current status
            $currentStatus = $this->finalConfirmationRepository->getCurrentStatus($changeRequest->id);

            if (!$currentStatus) {
                return [
                    'success' => false,
                    'message' => "Unable to determine current status for CR #{$crNumber}."
                ];
            }

            if ($changeRequest->inFinalState()) {
                return [
                    'success' => false,
                    'message' => "CR #{$crNumber} is already in a final state and cannot be modified."
                ];
            }

            if ($changeRequest->isAlreadyCancelledOrRejected()) {
                return [
                    'success' => false,
                    'message' => "CR #{$crNumber} is already Cancelled or Rejected and cannot be modified."
                ];
            }

            // Update the change request status
            $updateResult = $this->updateChangeRequestStatus(
                $changeRequest->id,
                $currentStatus->new_status_id,
                $statusId,
                $userId,
                $technical_feedback
            );

            if ($updateResult) {

                $actionText = $statusId === $this->getRejectStatusId() ? 'rejected' : 'cancelled';

                return [
                    'success' => true,
                    'message' => "CR #{$crNumber} has been successfully {$actionText}."
                ];
            }

            return [
                'success' => false,
                'message' => "Failed to update CR #{$crNumber} status. Please try again."
            ];

        } catch (\Exception $e) {
            Log::error('Error in processFinalConfirmation', [
                'cr_number' => $crNumber,
                'action' => $statusId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'An unexpected error occurred while processing the final confirmation.'
            ];
        }
    }

    /**
     * Update change request status using the existing repository pattern
     *
     * @param int $crId
     * @param int $oldStatusId
     * @param int $newStatusId
     * @param int $userId
     * @return bool
     */
    private function updateChangeRequestStatus(int $crId, int $oldStatusId, int $newStatusId, int $userId, string $technical_feedback): bool
    {
        try {
            // Create request object similar to handleDivisionManagerAction1
            $updateRequest = new Request([
                'old_status_id' => $oldStatusId,
                'new_status_id' => $newStatusId,
                'user_id' => $userId
            ]);

            // Use the new final confirmation method
            $result = $this->changeRequestRepository->updateChangeRequestStatusForFinalConfirmation($crId, $updateRequest, $technical_feedback);


            return $result !== false;

        } catch (\Exception $e) {
            Log::error('Error updating change request status in final confirmation', [
                'cr_id' => $crId,
                'old_status_id' => $oldStatusId,
                'new_status_id' => $newStatusId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
