<?php

namespace App\Services;

//use PhpEws\EwsClient;
use jamesiarmes\PhpEws\Client;
use jamesiarmes\PhpEws\Request\FindItemType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;
use jamesiarmes\PhpEws\Type\ItemResponseShapeType;
use jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use jamesiarmes\PhpEws\Type\TargetFolderIdType;
use jamesiarmes\PhpEws\Type\IndexedPageViewType;
use jamesiarmes\PhpEws\Enumeration\BodyTypeResponseType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use jamesiarmes\PhpEws\Enumeration\ItemQueryTraversalType;
use jamesiarmes\PhpEws\Request\GetItemType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use App\Models\Change_request;

class EwsMailReader
{
    protected $client;

    public function __construct()
    {
        $host = config('services.ews.host');        
        $username = config('services.ews.username'); 
        $password = config('services.ews.password');

        $this->client = new Client($host, $username, $password, Client::VERSION_2016);
    }

    public function readInbox($limit = 5)
    {
        
        //Find items in Inbox
        $findRequest = new FindItemType();

        $findRequest->ItemShape = new ItemResponseShapeType();
        $findRequest->ItemShape->BaseShape = DefaultShapeNamesType::ID_ONLY;

        $folderId = new DistinguishedFolderIdType();
        $folderId->Id = DistinguishedFolderIdNameType::INBOX;

        $findRequest->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
        $findRequest->ParentFolderIds->DistinguishedFolderId[] = $folderId;

        $findRequest->Traversal = ItemQueryTraversalType::SHALLOW;

        $view = new IndexedPageViewType();
        $view->BasePoint = 'Beginning';
        $view->Offset = 0;
        $view->MaxEntriesReturned = 20;
        $findRequest->IndexedPageItemView = $view;

        $findResponse = $this->client->FindItem($findRequest);
        $items = [];

        /*if (!isset($findResponse->ResponseMessages->FindItemResponseMessage)) {
            return []; //Return empty array if no messages
        }*/

        foreach ($findResponse->ResponseMessages->FindItemResponseMessage as $responseMessage) {
            if ($responseMessage->ResponseClass !== 'Success') continue;

            foreach ($responseMessage->RootFolder->Items->Message as $message) {
                $items[] = $message->ItemId;
            }
        }
        //dd($items);
        if (empty($items)) {
            return [];
        }
        //Step 2: Use GetItem to fetch full content
        $getRequest = new GetItemType();
        $getRequest->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $getRequest->ItemIds->ItemId = $items;

        $getRequest->ItemShape = new ItemResponseShapeType();
        $getRequest->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $getRequest->ItemShape->BodyType = BodyTypeResponseType::HTML;

        $getResponse = $this->client->GetItem($getRequest);

        $results = [];

        foreach ($getResponse->ResponseMessages->GetItemResponseMessage as $messageResponse) {
            if ($messageResponse->ResponseClass !== 'Success') continue;

            foreach ($messageResponse->Items->Message as $msg) {
                $results[] = [
                    'subject' => $msg->Subject ?? '(No Subject)',
                    'from'    => $msg->From->Mailbox->EmailAddress ?? '(Unknown)',
                    'date'    => $msg->DateTimeReceived ?? '',
                    'body'    => $msg->Body ? $msg->Body->_ : '(No Body)',
                ];
            }
        }

        //$this->moveToArchive($items);
        return $results;
        
    }

    protected function determineAction(string $text): ?string
    {
        $text = strtolower($text);
        if (strpos($text, 'approved') !== false) {
            return 'approved';
        }
        if (strpos($text, 'rejected') !== false) {
            return 'rejected';
        }
        return null;
    }

    
    public function handleApprovals(int $limit = 20): void
    {
        $messages = $this->readInbox($limit);

        foreach ($messages as $message) {

            if (!preg_match('/CR\s*#(\d+)\s*-.*Awaiting Your Approval/i', $message['subject'], $m)) {
                continue; 
            }

            $crNo      = (int) $m[1];
            $crId      = Change_request::where('cr_no', $crNo)->value('id');
            $bodyPlain = strip_tags($message['body']);
            $action    = $this->determineAction($bodyPlain);

            if (!$action) {
                continue; 
            }

            $this->processCrAction($crId, $action, $message['from']);
        }
    }

    
    //Execute repository logic to update CR status.
     
    protected function processCrAction(int $crId, string $action, string $fromEmail): void
    {
        $cr = \App\Models\Change_request::find($crId);
        if (!$cr) {
            \Log::warning("EWS Mail Reader: CR #{$crId} not found whilst processing {$action} from {$fromEmail}");
            return;
        }

        //Ensure the sender is the assigned division manager
        if (strtolower($fromEmail) !== strtolower($cr->division_manager)) {
            \Log::warning("EWS Mail Reader: Unauthorized {$action} attempt for CR #{$crId} from {$fromEmail}");
            return;
        }

        
        $currentStatus = \App\Models\Change_request_statuse::where('cr_id', $crId)
            ->where('active', '1')
            ->value('new_status_id');
        if($currentStatus != '22'){
            \Log::warning("EWS Mail Reader: CR #{$crId} is not in pending cap status whilst processing {$action} from {$fromEmail}");
            return;
        }
        
        if ($cr->workflow_type_id == 3) {
            $newStatus = $action === 'approved' ? 36 : 35;
        } elseif ($cr->workflow_type_id == 5) {
            $newStatus = $action === 'approved' ? 188 : 184;
        } else {
            \Log::warning("EWS Mail Reader: Unsupported workflow_type_id {$cr->workflow_type_id} for CR #{$crId}");
            return;
        }

        $repo = new \App\Http\Repository\ChangeRequest\ChangeRequestRepository();

        $req = new \Illuminate\Http\Request([
            'old_status_id' => $currentStatus,
            'new_status_id' => $newStatus,
            //propagate sender email for repo user resolution logic
            'assign_to'     => null,
        ]);

        try {
            $repo->UpateChangeRequestStatus($crId, $req);
            \Log::info("EWS Mail Reader: CR #{$crId} {$action}d successfully by {$fromEmail}");
        } catch (\Throwable $e) {
            \Log::error("EWS Mail Reader: Failed to {$action} CR #{$crId} → " . $e->getMessage());
        }
    }
    // not works need fixing
    
    protected function getOrCreateArchiveFolder()
    {
        // find the Archive folder
        $findFolder = new \jamesiarmes\PhpEws\Request\FindFolderType();
        $findFolder->Traversal = \jamesiarmes\PhpEws\Enumeration\FolderQueryTraversalType::DEEP;
        $findFolder->FolderShape = new \jamesiarmes\PhpEws\Type\FolderResponseShapeType();
        $findFolder->FolderShape->BaseShape = \jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType::ALL_PROPERTIES;
        
        // Search in the root folder
        $parentFolder = new \jamesiarmes\PhpEws\Type\DistinguishedFolderIdType();
        $parentFolder->Id = \jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType::INBOX;
        $findFolder->ParentFolderIds = new \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType();
        $findFolder->ParentFolderIds->DistinguishedFolderId[] = $parentFolder;
        
        // Filter for the Archive folder
        $findFolder->Restriction = new \jamesiarmes\PhpEws\Type\RestrictionType();
        $findFolder->Restriction->IsEqualTo = new \jamesiarmes\PhpEws\Type\IsEqualToType();
        $findFolder->Restriction->IsEqualTo->FieldURI = new \jamesiarmes\PhpEws\Type\PathToUnindexedFieldType();
        $findFolder->Restriction->IsEqualTo->FieldURI->FieldURI = 'folder:DisplayName';
        $findFolder->Restriction->IsEqualTo->FieldURIOrConstant = new \jamesiarmes\PhpEws\Type\FieldURIOrConstantType();
        $findFolder->Restriction->IsEqualTo->FieldURIOrConstant->Constant = new \jamesiarmes\PhpEws\Type\ConstantValueType();
        $findFolder->Restriction->IsEqualTo->FieldURIOrConstant->Constant->Value = 'Archives';
        
        try {
            $response = $this->client->FindFolder($findFolder);
            
            // If we found the Archive folder, return its ID
            if (isset($response->ResponseMessages->FindFolderResponseMessage->RootFolder->Folders->Folder[0])) {
                $folder = $response->ResponseMessages->FindFolderResponseMessage->RootFolder->Folders->Folder[0];
                return $folder->FolderId;
            }
        } catch (\Exception $e) {
            \Log::error('Error finding Archive folder: ' . $e->getMessage());
        }
        
    }

    protected function moveToArchive($items)
    {
        if (empty($items)) {
            return false;
        }

        try {
            // Get or create the Archive folder
            $archiveFolderId = $this->getOrCreateArchiveFolder();
            
            // Create move request
            $moveRequest = new \jamesiarmes\PhpEws\Request\MoveItemType();
            $moveRequest->ToFolderId = new \jamesiarmes\PhpEws\Type\TargetFolderIdType();
            $moveRequest->ToFolderId->FolderId = $archiveFolderId;
            
            // Add items to move
            $moveRequest->ItemIds = new \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType();
            $moveRequest->ItemIds->ItemId = $items;
            
            // Set to move (not copy)
            $moveRequest->ReturnNewItemIds = false;
            
            // Execute the move
            $response = $this->client->MoveItem($moveRequest);
            
            // Check for errors
            if (isset($response->ResponseMessages->MoveItemResponseMessage)) {
                foreach ($response->ResponseMessages->MoveItemResponseMessage as $message) {
                    if ($message->ResponseClass !== 'Success') {
                        \Log::error('Failed to move message to Archive: ' . 
                            ($message->MessageText ?? 'Unknown error'));
                        return false;
                    }
                }
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error moving messages to Archive: ' . $e->getMessage());
            return false;
        }
    }

}
