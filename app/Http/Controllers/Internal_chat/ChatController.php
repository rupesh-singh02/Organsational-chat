<?php

namespace App\Http\Controllers\Internal_chat;

use App\CustomClasses\CreateStorage;
use App\Http\Controllers\Controller;
use App\Models\OfficialChat;
use App\Models\OfficialChatDocument;
use App\Models\OfficialChatImage;
use App\Models\OfficialChatText;
use App\Models\OfficialChatVideo;
use App\Models\Staff;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    public function getActiveContacts()
    {
        try {
            // Get the authenticated user
            $currentUserId = Auth::id();

            // Get all active staff users except the authenticated user
            $active_staff = Staff::where('online_status', '1')
                ->where('id', '!=', $currentUserId)
                ->get();

            // Array to hold the contacts with last message details
            $contact_list = [];

            foreach ($active_staff as $staff) {
                // Retrieve the last sent and received messages between the current user and the staff
                $lastSentMessage = OfficialChat::where('to_staff_id', $staff->id)
                    ->where('from_staff_id', $currentUserId)
                    ->latest()
                    ->first();

                $lastReceivedMessage = OfficialChat::where('to_staff_id', $currentUserId)
                    ->where('from_staff_id', $staff->id)
                    ->latest()
                    ->first();

                // Compare both sent and received messages based on their created_at data and select the most recent one
                $lastChatMessage = $lastSentMessage;

                if ($lastReceivedMessage && (!$lastSentMessage || $lastReceivedMessage->created_at > $lastSentMessage->created_at)) {
                    $lastChatMessage = $lastReceivedMessage;
                }

                // Add the contact and last message data to the array
                $contactData = $staff->toArray();
                $contactData['last_chat_type'] = $lastChatMessage ? $lastChatMessage->message_type : null;
                if ($lastChatMessage && $lastChatMessage->message_type === 'text') {
                    $contactData['last_chat_message'] = $lastChatMessage->content ? $lastChatMessage->content->content : null;
                } elseif ($lastChatMessage && $lastChatMessage->message_type === 'image') {
                    $contactData['last_chat_message'] = $lastChatMessage->content ? $lastChatMessage->content : null;
                } else {
                    $contactData['last_chat_message'] = null;
                }

                $contactData['last_chat_time'] = $lastChatMessage ? $lastChatMessage->created_at : null;

                $contact_list[] = $contactData;

            }

            $message = "Retrieved active contacts and last chat data successfully";
            $status = true;

            return response()->json([
                'status' => $status,
                'message' => $message,
                'contact_list' => $contact_list,
                'type' => 'active',
                'active_user_count' => count($contact_list),
            ], 200);

        } catch (\Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $status = false;

            return response()->json(['status' => $status, 'message' => $message], 500);
        }
    }

    public function getInactiveContacts()
    {
        try {
            // Get the authenticated user
            $currentUserId = Auth::id();

            // Get all active staff users except the authenticated user
            $inactive_staff = Staff::where('online_status', '0')
                ->where('id', '!=', $currentUserId)
                ->get();

            // Array to hold the contacts with last message details
            $contact_list = [];

            foreach ($inactive_staff as $staff) {
                // Retrieve the last sent and received messages between the current user and the staff
                $lastSentMessage = OfficialChat::where('to_staff_id', $staff->id)
                    ->where('from_staff_id', $currentUserId)
                    ->latest()
                    ->first();

                $lastReceivedMessage = OfficialChat::where('to_staff_id', $currentUserId)
                    ->where('from_staff_id', $staff->id)
                    ->latest()
                    ->first();

                // Compare both sent and received messages based on their created_at data and select the most recent one
                $lastChatMessage = $lastSentMessage;

                if ($lastReceivedMessage && (!$lastSentMessage || $lastReceivedMessage->created_at > $lastSentMessage->created_at)) {
                    $lastChatMessage = $lastReceivedMessage;
                }

                // Add the contact and last message data to the array
                $contactData = $staff->toArray();
                $contactData['last_chat_type'] = $lastChatMessage ? $lastChatMessage->message_type : null;
                if ($lastChatMessage && $lastChatMessage->message_type === 'text') {
                    $contactData['last_chat_message'] = $lastChatMessage->content ? $lastChatMessage->content->content : null;
                } elseif ($lastChatMessage && $lastChatMessage->message_type === 'image') {
                    $contactData['last_chat_message'] = $lastChatMessage->content ? $lastChatMessage->content : null;
                } else {
                    $contactData['last_chat_message'] = null;
                }

                $contactData['last_chat_time'] = $lastChatMessage ? $lastChatMessage->created_at : null;

                $contact_list[] = $contactData;

            }

            $message = "Retrieved active contacts and last chat data successfully";
            $status = true;

            return response()->json([
                'status' => $status,
                'message' => $message,
                'contact_list' => $contact_list,
                'type' => 'inactive',
            ], 200);

        } catch (\Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $status = false;

            return response()->json(['status' => $status, 'message' => $message], 500);
        }

    }

    public function viewAndLoadChatData($staff_id)
    {
        try {
            // Get the authenticated user
            $sender = Auth::user();

            // Get the receiver details
            $receiver = Staff::findOrFail($staff_id);

            if (!$receiver) {
                return response()->json(['error' => 'Staff not found'], 404);
            }

            // Retrieve the last sent and received messages between the current user and the receiver
            $officialChat_sent = OfficialChat::where('to_staff_id', $receiver->id)
                ->where('from_staff_id', $sender->id)
                ->get();
            $officialChat_received = OfficialChat::where('to_staff_id', $sender->id)
                ->where('from_staff_id', $receiver->id)
                ->get();

            foreach ($officialChat_sent as $message) {

                if ($message->message_type === "text") {
                    $message->content;
                } else if ($message->message_type === "image") {
                    $message->imageContent->content = json_decode($message->imageContent->content);
                    $message->imageContent->type = json_decode($message->imageContent->type);
                    $message->imageContent->size = json_decode($message->imageContent->size);
                } else if ($message->message_type === "video") {
                    $message->videoContent->content = json_decode($message->videoContent->content);
                    $message->videoContent->type = json_decode($message->videoContent->type);
                    $message->videoContent->size = json_decode($message->videoContent->size);
                } else if ($message->message_type === "document") {
                    $message->documentContent;
                }

                $message->sender;
                $message->receiver;

                if ($message->reply_id !== null) {
                    $message->replyDetails;

                    if ($message->replyDetails->message_type === "image") {
                        $message->replyDetails->imageContent->content = json_decode($message->replyDetails->imageContent->content);
                        $message->replyDetails->imageContent->type = json_decode($message->replyDetails->imageContent->type);
                        $message->replyDetails->imageContent->size = json_decode($message->replyDetails->imageContent->size);
                    } else if ($message->replyDetails->message_type === "video") {
                        $message->replyDetails->videoContent->content = json_decode($message->replyDetails->videoContent->content);
                        $message->replyDetails->videoContent->type = json_decode($message->replyDetails->videoContent->type);
                        $message->replyDetails->videoContent->size = json_decode($message->replyDetails->videoContent->size);
                    }
                }

            }

            // Process received messages
            foreach ($officialChat_received as $message) {

                if ($message->message_type === "text") {
                    $message->content;
                } else if ($message->message_type === "image") {
                    $message->imageContent->content = json_decode($message->imageContent->content);
                    $message->imageContent->type = json_decode($message->imageContent->type);
                    $message->imageContent->size = json_decode($message->imageContent->size);
                } else if ($message->message_type === "video") {
                    $message->videoContent->content = json_decode($message->videoContent->content);
                    $message->videoContent->type = json_decode($message->videoContent->type);
                    $message->videoContent->size = json_decode($message->videoContent->size);
                } else if ($message->message_type === "document") {
                    $message->documentContent;
                }

                $message->sender;
                $message->receiver;

                if ($message->reply_id !== null) {
                    $message->replyDetails;

                    if ($message->replyDetails->message_type === "image") {
                        $message->replyDetails->imageContent->content = json_decode($message->replyDetails->imageContent->content);
                        $message->replyDetails->imageContent->type = json_decode($message->replyDetails->imageContent->type);
                        $message->replyDetails->imageContent->size = json_decode($message->replyDetails->imageContent->size);
                    } else if ($message->replyDetails->message_type === "video") {
                        $message->replyDetails->videoContent->content = json_decode($message->replyDetails->videoContent->content);
                        $message->replyDetails->videoContent->type = json_decode($message->replyDetails->videoContent->type);
                        $message->replyDetails->videoContent->size = json_decode($message->replyDetails->videoContent->size);
                    }
                }
            }

            // Combine both collections
            $combinedChats = $officialChat_sent->concat($officialChat_received);

            // Sort the combined collection by 'created_at' in ascending order
            $sortedChatData = $combinedChats->sortBy('created_at');

            // Group the sorted chat data by date
            $groupedChatData = $sortedChatData->groupBy(function ($chat) {

                $sender = Auth::user();
                $type = ($chat->from_staff_id === $sender->id) ? 'sent' : 'received';
                $chat->type = $type;
                $chat->current_user = Auth::id();

                // Extract the date from the 'created_at' field
                return $chat->created_at->toDateString();
            });

            // Create an array to hold the grouped chat data with date as key
            $chatDataByDate = new \stdClass();

            // Iterate over the grouped chat data and format it as needed
            foreach ($groupedChatData as $date => $chats) {
                // Assign the formatted chat data to the date key
                $chatDataByDate->$date = $chats;
            }

            $message = "Retrieved staff details and chat data successfully";
            $status = true;

            return response()->json([
                'status' => $status,
                'message' => $message,
                'chat_data' => $chatDataByDate,
                'receiver_staff' => $receiver,
            ], 200);

        } catch (ValidationException $e) {
            // Validation error
            $message = "Validation error: " . json_encode($e->validator->getMessageBag()->toArray());
            $status = false;

            return response()->json([
                'status' => $status,
                'error' => $message,
            ], 422);

        } catch (ModelNotFoundException $e) {
            $message = 'Unauthenticated';
            $status = false;

            return response()->json([
                'status' => $status,
                'message' => $message,
            ], 404);

        } catch (\Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $status = false;

            return response()->json(['status' => $status, 'message' => $message], 500);
        }
    }

    public function updateMessageStatus($staff_id)
    {
        try {
            // Get the authenticated user
            $sender = Auth::user();

            // Get the receiver details
            $receiver = Staff::findOrFail($staff_id);

            if (!$receiver) {
                return response()->json(['error' => 'Staff not found'], 404);
            }

            // List of unread messages before update
            $officialChat_old = OfficialChat::where('to_staff_id', $sender->id)
                ->where('from_staff_id', $receiver->id)
                ->get();

            // Update view_status to 1 for all retrieved messages
            OfficialChat::where('to_staff_id', $sender->id)
                ->where('from_staff_id', $receiver->id)
                ->where('view_status', 0)
                ->update(['view_status' => 1]);

            // List of unread messages after update
            $officialChat_new = OfficialChat::where('to_staff_id', $sender->id)
                ->where('from_staff_id', $receiver->id)
                ->get();

            // Return the updated messages along with count
            $message = "Retrieved unread chat data successfully";
            $status = true;

            return response()->json([
                'status' => $status,
                'message' => $message,
            ], 200);

        } catch (ValidationException $e) {
            // Validation error
            $message = "Validation error: " . json_encode($e->validator->getMessageBag()->toArray());
            $status = false;

            return response()->json([
                'status' => $status,
                'error' => $message,
            ], 422);

        } catch (ModelNotFoundException $e) {
            $message = 'Unauthenticated';
            $status = false;

            return response()->json([
                'status' => $status,
                'message' => $message,
            ], 404);

        } catch (\Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $status = false;

            return response()->json(['status' => $status, 'message' => $message], 500);
        }
    }

    public function sendChat(Request $request)
    {
        $storage = new CreateStorage();

        try {
            $staff = Auth::guard('staff')->user();

            if (!$staff) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $input = $request->all();

            $validation = Validator::make($input, [
                'message' => ['required', function ($attribute, $value, $fail) {
                    // Allowed MIME types for images, videos, and documents
                    $allowedImageTypes = ['jpeg', 'jpg', 'png', 'svg', 'webp', 'bmp'];
                    $allowedVideoTypes = ['gif', 'mp4', 'mov', 'avi'];
                    $allowedDocumentTypes = [
                        'pdf',
                        'excel',
                        'vnd.ms-excel',
                        'x-excel',
                        'x-msexcel',
                        'vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'doc',
                        'ms-doc',
                        'msword',
                        'vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'vnd.ms-powerpoint',
                        'vnd.openxmlformats-officedocument.presentationml.presentation',
                        'vnd.openxmlformats-officedocument.presentationml.slideshow',
                    ];

                    if (is_array($value)) {
                        // Check if the message is an array of base64 encoded images or videos
                        foreach ($value as $message) {

                            // Check if the message is a base64 encoded image
                            if (preg_match('/^data:image\/(' . implode('|', $allowedImageTypes) . ');base64,/', $message)) {
                                continue; // Valid base64 image
                            }

                            // Check if the message is a base64 encoded video
                            if (preg_match('/^data:video\/(' . implode('|', $allowedVideoTypes) . ');base64,/', $message)) {
                                continue; // Valid base64 video
                            }

                            // If neither a valid image nor video, fail validation
                            $fail('Each element of ' . $attribute . ' must be either a base64 encoded image or a base64 encoded video.');
                        }

                        return true; // All elements of the array are valid
                    }

                    // Check if the message is a base64 encoded document
                    if (preg_match('/^data:application\/(' . implode('|', $allowedDocumentTypes) . ');base64,/', $value)) {
                        return true; // Valid base64 document
                    }

                    // Check if the message is text
                    if (is_string($value)) {

                        if (strlen($value) > 1000) {
                            $fail('The ' . $attribute . ' must not exceed 1000 characters.');
                        }
                        return true;
                    }

                    // If neither text nor base64 image nor document, fail validation
                    $fail('The ' . $attribute . ' must be either a text message, a base64 encoded image, or a base64 encoded document.');
                }],
                'to_staff_id' => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()], 400);
            }

            $data['from_staff_id'] = $staff->id;
            $data['to_staff_id'] = $request->to_staff_id;
            $data['message'] = $request->message;
            $data['view_status'] = 0;

            if ($request->reply_id !== null) {
                $data['reply_id'] = $request->reply_id;
            }

            // Extracted method to handle file upload and save to database
            if (is_array($input['message'])) {

                $fileUrls = [];
                $fileTypes = [];
                $fileSizes = [];
                $count = 1;

                foreach ($input['message'] as $key => $mediaData) {

                    if (preg_match('/^data:image\/(\w+);base64,/', $mediaData)) {

                        // Handle base64 encoded image
                        $imageExtension = explode('/', explode(';', $mediaData)[0])[1];
                        $fileTypes[] = $imageExtension;

                        $imageName = 'image_' . time() . '_' . $count . '.' . $imageExtension;
                        $imageData = substr($mediaData, strpos($mediaData, ',') + 1);
                        $decodedImage = base64_decode($imageData);

                        $path = $storage->getStoragePath("images");

                        if (!Storage::exists($path)) {
                            Storage::makeDirectory($path, 0777, true);
                        }

                        $location = $path . '/' . $imageName;

                        $filePath = Storage::disk('files_disk')->put($location, $decodedImage);

                        $fileUrls[] = 'https://chatfiles.ntscabs.com/public' . $location;

                        $imageSize = Storage::disk('files_disk')->size($location);
                        $fileSizes[] = round($imageSize / 1048576, 2) . ' MB';

                        $data['message_type'] = 'image';

                        $count++;

                    } elseif (isset($input['message']['media_1']) && preg_match('/^data:video\/(\w+);base64,/', $input['message']['media_1'])) {
                        // Handle base64 encoded video
                        $video_file = $input['message']['media_1'];

                        $videoExtension = explode('/', explode(';', $video_file)[0])[1];
                        $fileTypes[] = $videoExtension;

                        $videoName = 'video_' . time() . '.' . $videoExtension;
                        $videoData = substr($video_file, strpos($video_file, ',') + 1);
                        $decodedVideo = base64_decode($videoData);

                        $path = $storage->getStoragePath("videos");

                        if (!Storage::exists($path)) {
                            Storage::makeDirectory($path, 0777, true);
                        }

                        $location = $path . '/' . $videoName;

                        $filePath = Storage::disk('files_disk')->put($location, $decodedVideo);

                        $fileUrls[] = 'https://chatfiles.ntscabs.com/public' . $location;

                        $videoSize = Storage::disk('files_disk')->size($location);
                        $fileSizes[] = round($videoSize / 1048576, 2) . ' MB';

                        $data['message_type'] = 'video';

                    }

                }

                if ($data['message_type'] === "image") {

                    $officialChat = OfficialChat::create($data);

                    $officialChatImage = OfficialChatImage::create([
                        'official_chat_id' => $officialChat->id,
                        'content' => json_encode($fileUrls),
                        'type' => json_encode($fileTypes),
                        'size' => json_encode($fileSizes),
                    ]);

                    $content = [
                        'content' => $officialChatImage->content,
                        'type' => $officialChatImage->type,
                        'size' => $officialChatImage->size,
                        'created_at' => $officialChat->created_at,
                    ];
                } else if ($data['message_type'] === "video") {

                    $officialChat = OfficialChat::create($data);

                    $officialChatVideo = OfficialChatVideo::create([
                        'official_chat_id' => $officialChat->id,
                        'content' => json_encode($fileUrls),
                        'type' => json_encode($fileTypes),
                        'size' => json_encode($fileSizes),
                    ]);

                    $content = [
                        'content' => $officialChatVideo->content,
                        'type' => $officialChatVideo->type,
                        'size' => $officialChatVideo->size,
                        'created_at' => $officialChat->created_at,
                    ];
                }

            } else if (is_string($input['message'])) {

                if (preg_match('/^data:application\/([\w.-]+);base64,/', $input['message'], $matches)) {
                    // Handle base64 encoded document
                    $docExtension = $matches[1];
                    $docType = $matches[1];

                    // Mapping extensions and types
                    $extensionMap = [
                        'pdf' => 'pdf',
                        'excel' => 'xls',
                        'vnd.ms-excel' => 'xls',
                        'x-excel' => 'xls',
                        'x-msexcel' => 'xls',
                        'vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                        'doc' => 'doc',
                        'ms-doc' => 'doc',
                        'msword' => 'doc',
                        'vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                        'vnd.ms-powerpoint' => 'ppt',
                        'vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
                        'vnd.openxmlformats-officedocument.presentationml.slideshow' => 'ppsx',
                    ];

                    $typeMap = [
                        'pdf' => 'pdf',
                        'excel' => 'excel',
                        'vnd.ms-excel' => 'excel',
                        'x-excel' => 'excel',
                        'x-msexcel' => 'excel',
                        'vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'excel',
                        'doc' => 'word',
                        'ms-doc' => 'word',
                        'msword' => 'word',
                        'vnd.openxmlformats-officedocument.wordprocessingml.document' => 'word',
                        'vnd.ms-powerpoint' => 'ppt',
                        'vnd.openxmlformats-officedocument.presentationml.presentation' => 'ppt',
                        'vnd.openxmlformats-officedocument.presentationml.slideshow' => 'ppt',
                    ];

                    if (array_key_exists($docExtension, $extensionMap)) {
                        $docExtension = $extensionMap[$docExtension];
                    } else {
                        throw new \Exception('Unsupported document type');
                    }

                    if (array_key_exists($docType, $typeMap)) {
                        $docType = $typeMap[$docType];
                    } else {
                        throw new \Exception('Unsupported document type');
                    }

                    $docName = 'document_' . time() . '.' . $docExtension;
                    $docData = substr($input['message'], strpos($input['message'], ',') + 1);
                    $decodedDoc = base64_decode($docData);

                    $path = $storage->getStoragePath("documents");

                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path, 0777, true);
                    }

                    $location = $path . '/' . $docName;

                    $filePath = Storage::disk('files_disk')->put($location, $decodedDoc);

                    $fileUrl = 'https://chatfiles.ntscabs.com/public' . $location;

                    $docSize = Storage::disk('files_disk')->size($location);
                    $docSizeMB = round($docSize / 1048576, 2);

                    $data['message_type'] = 'document';

                    $officialChat = OfficialChat::create($data);

                    $officialChatDocument = OfficialChatDocument::create([
                        'official_chat_id' => $officialChat->id,
                        'content' => $fileUrl,
                        'type' => $docExtension,
                        'doc_type' => $docType,
                        'size' => $docSizeMB . ' MB',
                    ]);

                    $content = [
                        'content' => $officialChatDocument->content,
                        'type' => $officialChatDocument->type,
                        'doc_type' => $officialChatDocument->doc_type,
                        'size' => $officialChatDocument->size,
                        'created_at' => $officialChat->created_at,
                    ];

                } else {
                    // Handle text message
                    $data['message_type'] = 'text';

                    $officialChat = OfficialChat::create($data);

                    $officialChatText = OfficialChatText::create([
                        'official_chat_id' => $officialChat->id,
                        'content' => $input['message'],
                    ]);

                    $content = [
                        'content' => $input['message'],
                        'created_at' => $officialChat->created_at,
                    ];
                }
            }

            $todayDate = now()->toDateString();

            if ($data['message_type'] === "text") {
                return response()->json([
                    $todayDate => [
                        'id' => $officialChat->id,
                        'content' => $content,
                        'message_type' => $data['message_type'],
                        'type' => 'sent',
                    ],
                ], 200);
            } elseif ($data['message_type'] === "image") {
                return response()->json([
                    $todayDate => [
                        'id' => $officialChat->id,
                        'image_content' => $content,
                        'message_type' => $data['message_type'],
                        'type' => 'sent',
                    ],
                ], 200);
            } elseif ($data['message_type'] === "video") {
                return response()->json([
                    $todayDate => [
                        'id' => $officialChat->id,
                        'video_content' => $content,
                        'message_type' => $data['message_type'],
                        'type' => 'sent',
                    ],
                ], 200);
            } elseif ($data['message_type'] === "document") {
                return response()->json([
                    $todayDate => [
                        'id' => $officialChat->id,
                        'document_content' => $content,
                        'message_type' => $data['message_type'],
                        'type' => 'sent',
                    ],
                ], 200);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error: ' . $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

}
