<?php

namespace Workdo\MailBox\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;
use Webklex\IMAP\Message;
use Illuminate\Support\Facades\Validator;
use Webklex\IMAP\Facades\Client;
use Workdo\MailBox\Emails\sendMail;
use Workdo\MailBox\Emails\ReplyMail;
use Illuminate\Support\Facades\Mail;
use Workdo\MailBox\Emails\EmailsSendMail;
use Workdo\MailBox\Entities\MailboxCredentail;
use Illuminate\Support\Facades\Request as RequestFacade;

class MailBoxController extends Controller
{

	public function setConfig()
	{
		$mail_credentail = MailboxCredentail::where('workspace_id', getActiveWorkSpace())->where('created_by', Auth::user()->id)->first();
		config(['imap.accounts.default.host' => (!empty($mail_credentail->emailbox_mail_host) && isset($mail_credentail->emailbox_mail_host)) ? $mail_credentail->emailbox_mail_host : ""]);
		config(['imap.accounts.default.port' => (!empty($mail_credentail->emailbox_incoming_port) && isset($mail_credentail->emailbox_incoming_port)) ? $mail_credentail->emailbox_incoming_port : "993"]);
		config(['imap.accounts.default.encryption' => (!empty($mail_credentail->emailbox_mail_encryption) && isset($mail_credentail->emailbox_mail_encryption)) ? $mail_credentail->emailbox_mail_encryption : "ssl"]);
		config(['imap.accounts.default.username' => (!empty($mail_credentail->emailbox_mail_username) && isset($mail_credentail->emailbox_mail_username)) ? $mail_credentail->emailbox_mail_username : ""]);
		config(['imap.accounts.default.password' => (!empty($mail_credentail->emailbox_mail_password) && isset($mail_credentail->emailbox_mail_password)) ? $mail_credentail->emailbox_mail_password : ""]);


		return Client::make([
			'host'          => config('imap.accounts.default.host'),
			'port'          => config('imap.accounts.default.port'),
			'encryption'    => config('imap.accounts.default.encryption'),
			'username'      => config('imap.accounts.default.username'),
			'password'      => config('imap.accounts.default.password'),
			'protocol'      => 'imap'
		]);
	}
	public function setting(Request $request)
	{
		if (Auth::user()->isAbleTo('Emailbox manage')) {
			if ($request->has('mailbox_is_on')) {
				$validator = Validator::make(
					$request->all(),
					[
						'mailbox_imap_server' => 'required|string',
						'mailbox_imap_port' => 'required',
						'mailbox_encryption' => 'required'
					]
				);
				if ($validator->fails()) {
					$messages = $validator->getMessageBag();

					return redirect()->back()->with('error', $messages->first());
				}
			}
			$post = $request->all();
			unset($post['_token']);
			unset($post['_method']);
			if ($request->has('mailbox_is_on')) {
				foreach ($post as $key => $value) {
					// Define the data to be updated or inserted
					$data = [
						'key' => $key,
						'workspace' => getActiveWorkSpace(),
						'created_by' => creatorId(),
					];

					// Check if the record exists, and update or insert accordingly
					Setting::updateOrInsert($data, ['value' => $value]);
				}
			} else {
				// Define the data to be updated or inserted
				$data = [
					'key' => 'mailbox_is_on',
					'workspace' => getActiveWorkSpace(),
					'created_by' => creatorId(),
				];

				// Check if the record exists, and update or insert accordingly
				Setting::updateOrInsert($data, ['value' => 'off']);
			}

			// Settings Cache forget
			AdminSettingCacheForget();
			comapnySettingCacheForget();
			return redirect()->back()->with('success', 'EMail Box setting save sucessfully.');
		} else {
			return redirect()->back()->with('error', __('Permission denied.'));
		}
	}
	/**
	 * Display a listing of the resource.
	 * @return Renderable
	 */
	public function index($type = "")
	{
		if (Auth::user()->isAbleTo('Emailbox manage')) {

			try {
				$client = $this->setConfig();

				if ($type == "sent") {
					if($client->getFolder('Sent'))
                    {
                        $folder = $client->getFolder('Sent');
                    }
                    else
                    {
                        $folder = $client->getFolder('Sent Mail');
                    }
				} elseif ($type == "trash") {
					if ($client->getFolder('Bin')) {
						$folder = $client->getFolder('Bin');
					} else {
						$folder = $client->getFolder('Trash');
					}
				} elseif ($type == "drafts") {
					$folder = $client->getFolder('Drafts');
				} elseif ($type == "spam") {
					if ($client->getFolder('Spam')) {
						$folder = $client->getFolder('Spam');
					} else {
						$folder = $client->getFolder('Junk');
					}
				} elseif ($type == "archive") {
					$folder = $client->getFolder('Archive');
				} elseif ($type == 'starred') {
					$folder = $client->getFolder('Starred') ?? $client->getFolder('INBOX');
				} else {
					$folder = $client->getFolder('INBOX');
				}

				if ($type == 'starred') {
					$messages = $folder->messages()->all()->get()->filter(function ($message) {
						return $message->hasFlag('\\Flagged');
					});

					$page = request()->get('page', 1);
					$perPage = 10;
					$messages = new LengthAwarePaginator(
						$messages->slice(($page - 1) * $perPage, $perPage),
						$messages->count(),
						$perPage,
						$page,
						['path' => request()->url(), 'query' => request()->query()]
					);

				} else {
					$messages = $folder->query()->all()->paginate($per_page = 10, $page = null, $page_name = 'imap_page');
				}

				return view('mailbox::mail.index', compact('messages'));
			} catch (\Exception $e) {

				return redirect()->route('mailbox.configuration')->with('error', __('Unable to connect with imap server please set your credentail & set Email Box setting.'));
			}
		} else {
			return redirect()->back()->with('error', __('Permission denied.'));
		}
	}

	/**
	 * Show the form for creating a new resource.
	 * @return Renderable
	 */
	public function create($id = null, $type = null)
	{
		if (Auth::user()->isAbleTo('Emailbox mail sent')) {
			if (!empty($id) && !empty($type)) {
				try {
					$client = $this->setConfig();

					if ($type == "sent") {
						if($client->getFolder('Sent'))
                        {
                            $folder = $client->getFolder('Sent');
                        }
                        else
                        {
                            $folder = $client->getFolder('Sent Mail');
                        }
					} elseif ($type == "trash") {
						$folder = $client->getFolderByName('Trash');
					} elseif ($type == "drafts") {
						$folder = $client->getFolder('Drafts');
					} elseif ($type == "spam") {
						$folder = $client->getFolderByName('spam');
					} elseif ($type == "archive") {
						$folder = $client->getFolder('Archive');
					} else {
						$folder = $client->getFolder('INBOX');
					}
					$message = $folder->query()->getMessageByMsgn($id);

					if ($message) {
						return view('mailbox::mail.create')->with('message', $message);
					} else {
						return redirect()->route('mailbox.index', $type)->with('error', "Unable to fetch mail");
					}
				} catch (\Throwable $th) {
					return redirect()->route('mailbox.configuration')->with('error', __('Unable to connect with imap server please set your credentail & set Email Box setting.'));
				}
			} else {
				return view('mailbox::mail.create');
			}
		} else {
			return redirect()->back()->with('error', __('Permission denied.'));
		}
	}
	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return Renderable
	 */
	public function store(Request $request)
	{
		if (Auth::user()->isAbleTo('Emailbox mail sent')) {
			$client = $this->setConfig();
			$attachments = [];
			if ($request->file('attachment')) {
				foreach ($request->file('attachment') as $key => $file) {

					$filenameWithExt = $request->file('attachment')[$key]->getClientOriginalName();
					$filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
					$extension       = $request->file('attachment')[$key]->getClientOriginalExtension();
					$fileNameToStore = $filename . '_' . time() . '.' . $extension;

					$uplaod = multi_upload_file($file, 'attachment', $fileNameToStore, 'mail_attachment');

					if ($uplaod['flag'] == 1) {
						$attachments[] = $uplaod['url'];
					}
				}
			}
			$subject = $request->subject;
			$message = $request->content;
			$to = $request->to;
			$cc = $request->cc;
			$email = new EmailsSendMail($subject, $message);

			$email->to($to);
			if ($cc) {
				$email->cc($cc);
			}
			foreach ($attachments as $attachment) {
				$email->attach($attachment);
			}
			$user = Auth::user();
			$setconfing = $this->SetConfigEmailBoxMail();
			if ($setconfing ==  true) {
				try {
					Mail::send($email);

					if (!empty($request->mail_id)) {
						$folder = $client->getFolder('Drafts');

						$messages = $folder->query()->getMessageByMsgn($request->mail_id);

						if ($messages) {
                            if($client->getFolder('Sent'))
                            {
                                $moveToFolder = $client->getFolder('Sent');
                            }
                            else
                            {
                                $moveToFolder = $client->getFolder('Sent Mail');
                            }
							$message->move($moveToFolder->path);
						}
					} else {
                        if($client->getFolder('Sent'))
                        {
                            $folder = $client->getFolder('Sent');
                        }
                        else
                        {
                            $folder = $client->getFolder('Sent Mail');
                        }

						$mimeMessage = $this->createMimeMessage($subject, $message, $to, $cc, $attachments);
						$folder->appendMessage($mimeMessage);
					}

					return redirect()->route('mailbox.index', 'sent')->with('success', __('Mail sent successfully.'));
				} catch (\Exception $e) {

					return redirect()->route('mailbox.index', 'inbox')->with('error', $e);
				}
			} else {

				return redirect()->route('mailbox.index', 'inbox')->with('error', __('Something went wrong please try again'));
			}
		} else {
			return redirect()->route('mailbox.index', 'inbox')->with('error', __('Permission denied.'));
		}
	}


	protected function createMimeMessage($subject, $message, $to, $cc, $attachments)
	{
		$mail_credentail = MailboxCredentail::where('workspace_id', getActiveWorkSpace())->where('created_by', Auth::user()->id)->first();
		$headers = [
			'From' => $mail_credentail['emailbox_mail_from_address'],
			'To' => $to,
			'Subject' => $subject,
			'Date' => now()->toRfc1123String(),
			'Content-Type' => 'text/html; charset=UTF-8',
		];

		if (!empty($cc)) {
			$headers['Cc'] = $cc;
		}

		// Prepare the body and handle attachments (you may want to enhance this)
		$body = $message;

		// Handle attachments
		$attachmentHeaders = '';
		if (!empty($attachments)) {
			foreach ($attachments as $attachment) {
				$attachmentHeaders .= 'Content-Disposition: attachment; filename="' . basename($attachment) . '"\r\n';
				$attachmentHeaders .= 'Content-Type: application/octet-stream; name="' . basename($attachment) . '"\r\n';
				$attachmentHeaders .= 'Content-Transfer-Encoding: base64\r\n';
				$attachmentHeaders .= "\r\n" . chunk_split(base64_encode(file_get_contents($attachment))) . "\r\n";
			}
		}

		// Construct the MIME message
		$mimeMessage = "From: {$headers['From']}\r\n";
		$mimeMessage .= "To: {$headers['To']}\r\n";
		if (isset($headers['Cc'])) {
			$mimeMessage .= "Cc: {$headers['Cc']}\r\n";
		}
		$mimeMessage .= "Subject: {$headers['Subject']}\r\n";
		$mimeMessage .= "Date: {$headers['Date']}\r\n";
		$mimeMessage .= "Content-Type: {$headers['Content-Type']}\r\n";
		$mimeMessage .= "\r\n{$body}";
		$mimeMessage .= $attachmentHeaders;

		return $mimeMessage;
	}



	/**
	 * Show the specified resource.
	 * @param int $id
	 * @return Renderable
	 */
	public function show($id, $type)
	{

		try {
			$client = $this->setConfig();

			if ($type == "sent") {
                if($client->getFolder('Sent'))
                {
                    $folder = $client->getFolder('Sent');
                }
                else
                {
                    $folder = $client->getFolder('Sent Mail');
                }
			} elseif ($type == "trash") {
				if ($client->getFolder('Bin')) {
					$folder = $client->getFolder('Bin');
				} else {
					$folder = $client->getFolder('Trash');
				}
			} elseif ($type == "drafts") {
				$folder = $client->getFolder('Drafts');
			} elseif ($type == "spam") {
				if ($client->getFolder('Spam')) {
					$folder = $client->getFolder('Spam');
				} else {
					$folder = $client->getFolder('Junk');
				}
			} elseif ($type == "archive") {
				$folder = $client->getFolder('Archive');
			} else {
				$folder = $client->getFolder('INBOX');
			}

			$message = $folder->query()->getMessageByMsgn($id);

			if ($message) {


				return view('mailbox::mail.show')->with('message', $message);
			} else {
				return redirect()->route('mailbox.index', $type)->with('error', "Unable to fetch mail");
			}
		} catch (\Throwable $th) {
			return redirect()->route('mailbox.configuration')->with('error', __('Unable to connect with imap server please set your credentail & set Email Box setting.'));
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 * @param int $id
	 * @return Renderable
	 */
	public function edit($id)
	{
		return redirect()->back();
		return view('mailbox::edit');
	}

	/**
	 * Update the specified resource in storage.
	 * @param Request $request
	 * @param int $id
	 * @return Renderable
	 */
	public function update(Request $request, $id)
	{
		return redirect()->back();
	}

	/**
	 * Remove the specified resource from storage.
	 * @param int $id
	 * @return Renderable
	 */
	public function destroy($id, $type)
	{
		if (Auth::user()->isAbleTo('Emailbox mail delete')) {
			try {
				$client = $this->setConfig();

				if ($type == "sent") {
					if($client->getFolder('Sent'))
                    {
                        $folder = $client->getFolder('Sent');
                    }
                    else
                    {
                        $folder = $client->getFolder('Sent Mail');
                    }
				} elseif ($type == "trash") {
					$folder = $client->getFolderByName('Trash');
				} elseif ($type == "drafts") {
					$folder = $client->getFolder('Drafts');
				} elseif ($type == "spam") {
					$folder = $client->getFolderByName('spam');
				} elseif ($type == "archive") {
					$folder = $client->getFolder('Archive');
				} else {
					$folder = $client->getFolder('INBOX');
				}

				$message = $folder->query()->getMessageByMsgn($id);

				if ($message) {

					if ($type == 'trash') {
						$message->delete();
						return redirect()->route('mailbox.index', $type)->with('success', "Mail deleted successfully.");
					} else {
						$moveToFolder = $client->getFolder('Trash');
						$message->move($moveToFolder->path);
						return redirect()->route('mailbox.index', $type)->with('success', "Mail deleted successfully.");
					}
				} else {
					return redirect()->route('mailbox.index', $type)->with('error', "Mail can not delete.");
				}
			} catch (\Throwable $th) {

				return redirect()->route('mailbox.configuration')->with('error', __('Unable to connect with imap server please set your credentail & set Email Box setting.'));
			}
		} else {
			return redirect()->back()->with('error', __('Permission denied.'));
		}
	}

	public function action(Request $request)
	{
		if (Auth::user()->isAbleTo('Emailbox manage')) {
			try {
				$client = $this->setConfig();

				if ($request->folder == "sent") {
					if($client->getFolder('Sent'))
                    {
                        $folder = $client->getFolder('Sent');
                    }
                    else
                    {
                        $folder = $client->getFolder('Sent Mail');
                    }
				} elseif ($request->folder == "trash") {
					$folder = $client->getFolderByName('Trash');
				} elseif ($request->folder == "drafts") {
					$folder = $client->getFolder('Drafts');
				} elseif ($request->folder == "spam") {
					$folder = $client->getFolder('spam');
				} elseif ($request->folder == "archive") {
					$folder = $client->getFolder('Archive');
				} else {
					$folder = $client->getFolder('INBOX');
				}
				$result = "";
				foreach ($request->id as $id) {
					$message = $folder->query()->getMessageByMsgn($id);
					if ($request->action == 'starred') {
						$message->setFlag('Flagged');
						$flag = $message->getFlags();
						$result = ["status" => 1, 'msg' => __('Mail starred successfully.')];
					}
					if ($request->action == 'unstarred') {
						$message->removeFlag('Flagged');
						$flag = $message->getFlags();
						$flag = $message->getFlags();
						$result = ["status" => 0, 'msg' => __('Mail unstarred successfully.')];
					}
					if ($request->action == 'seen') {
						$message->setFlag('Seen');
						$flag = $message->getFlags();
						$result = ["status" => 1, 'msg' => __('Mail marked as seen successfully.')];
					}
					if ($request->action == 'unseen') {
						$message->removeFlag('Seen');
						$flag = $message->getFlags();
						$flag = $message->getFlags();
						$result = ["status" => 0, 'msg' => __('Mail marked as unseen successfully.')];
					}
					if ($request->action == 'move') {

						$moveToFolder = $client->getFolder($request->moveToFolder);
						$message->move($moveToFolder->path);
						$result = ["status" => 1, 'msg' => __('Mail move successfully')];
					}
				}
				return $result;
			} catch (\Exception $e) {
				return redirect()->route('mailbox.configuration')->with('error', __('Unable to connect with imap server please set your credentail & set Email Box setting.'));
			}
		} else {
			return redirect()->back()->with('error', __('Permission denied.'));
		}
	}
	public function move_mail(Request $request)
	{
		try {
			$client = $this->setConfig();

			if ($request->folder == "sent") {
				if($client->getFolder('Sent'))
                {
                    $folder = $client->getFolder('Sent');
                }
                else
                {
                    $folder = $client->getFolder('Sent Mail');
                }
			} elseif ($request->folder == "trash") {
				$folder = $client->getFolderByName('Trash');
			} elseif ($request->folder == "drafts") {
				$folder = $client->getFolder('Drafts');
			} elseif ($request->folder == "spam") {
				$folder = $client->getFolder('spam');
			} elseif ($request->folder == "archive") {
				$folder = $client->getFolder('Archive');
			} else {
				$folder = $client->getFolder('INBOX');
			}
			$result = "";
			$message = $folder->query()->getMessageByMsgn($request->id);
			$moveToFolder = $client->getFolder("INBOX");
			$message->move($moveToFolder->path);
			$result = ["status" => 1, 'msg' => __('Mail move to inbox successfully.'), 'folder' => $request->folder];
			return $result;
		} catch (Exception $e) {


			return redirect()->route('mailbox.configuration')->with('error', __('Unable to connect with imap server please set your credentail & set Email Box setting.'));
		}
	}
	public function reply($id, $type)
	{
		if (Auth::user()->isAbleTo('Emailbox mail reply')) {
			try {
				$client = $this->setConfig();

				if ($type == "sent") {
					if($client->getFolder('Sent'))
                    {
                        $folder = $client->getFolder('Sent');
                    }
                    else
                    {
                        $folder = $client->getFolder('Sent Mail');
                    }
				} elseif ($type == "trash") {
					$folder = $client->getFolderByName('Trash');
				} elseif ($type == "drafts") {
					$folder = $client->getFolder('Drafts');
				} elseif ($type == "spam") {
					$folder = $client->getFolderByName('spam');
				} elseif ($type == "archive") {
					$folder = $client->getFolder('Archive');
				} else {
					$folder = $client->getFolder('INBOX');
				}
				$message = $folder->query()->getMessageByMsgn($id);

				if ($message) {
					return view('mailbox::mail.reply')->with('message', $message);
				} else {
					return redirect()->route('mailbox.index', $type)->with('error', "Unable to fetch mail");
				}
			} catch (\Throwable $th) {

				return redirect()->route('mailbox.configuration')->with('error', __('Unable to connect with imap server please set your credentail & set Email Box setting.'));
			}
		} else {
			return redirect()->back()->with('error', __('Permission denied.'));
		}
	}
	public function reply_send(Request $request)
	{
		if (Auth::user()->isAbleTo('Emailbox mail reply')) {
			$attachments = [];
			if ($request->file('attachment')) {
				foreach ($request->file('attachment') as $key => $file) {

					$filenameWithExt = $request->file('attachment')[$key]->getClientOriginalName();
					$filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
					$extension       = $request->file('attachment')[$key]->getClientOriginalExtension();
					$fileNameToStore = $filename . '_' . time() . '.' . $extension;

					$uplaod = multi_upload_file($file, 'attachment', $fileNameToStore, 'mail_attachment');

					if ($uplaod['flag'] == 1) {
						$attachments[] = $uplaod['url'];
					}
				}
			}

			$subject = $request->subject;
			$content = $request->content;
			$to_mail = $request->to;
			$cc = $request->cc;
			$email = new ReplyMail($subject, $content, $to_mail);
			$email->to($to_mail);
			if ($cc) {
				$email->cc($cc);
			}
			foreach ($attachments as $attachment) {
				$email->attach($attachment);
			}
			$user = Auth::user();
			$setconfing = $this->SetConfigEmailBoxMail($user->id);

			if ($setconfing ==  true) {
				try {
					Mail::send($email);
					return redirect()->route('mailbox.index', 'sent')->with('success', __('Mail sent successfully.'));
				} catch (\Exception $e) {

					return redirect()->route('mailbox.index', 'inbox')->with('error', $e);
				}
			} else {

				return redirect()->route('mailbox.index', 'inbox')->with('error', __('Something went wrong please try again'));
			}
		} else {
			return redirect()->back()->with('error', __('Permission denied.'));
		}
	}
	public function configuration()
	{
		if (Auth::user()->isAbleTo('Emailbox manage')) {
			$mail_credentail = MailboxCredentail::where('workspace_id', getActiveWorkSpace())->where('created_by', Auth::user()->id)->first();
			return view('mailbox::mail.configuration', compact('mail_credentail'));
		} else {
			return redirect()->back()->with('error', __('Permission denied.'));
		}
	}
	public function configuration_store(Request $request)
	{
		if (Auth::user()->isAbleTo('Emailbox manage')) {
			$validator = \Validator::make(
				$request->all(),
				[
					'emailbox_mail_driver' => 'required|string|max:255',
					'emailbox_mail_host' => 'required|string|max:255',
					'emailbox_outgoing_port' => 'required|string|max:255',
					'emailbox_incoming_port' => 'required|string|max:255',
					'emailbox_mail_username' => 'required|string|max:255',
					'emailbox_mail_from_address' => 'required|string|max:255',
					'emailbox_mail_password' => 'required|string|max:255',
					'emailbox_mail_encryption' => 'required|string|max:255',
					'emailbox_mail_from_name' => 'required|string|max:255',

				]
			);
			if ($validator->fails()) {
				$messages = $validator->getMessageBag();

				return redirect()->back()->with('error', $messages->first());
			}
			$credentail = MailboxCredentail::where('workspace_id', getActiveWorkSpace())->where('created_by', Auth::user()->id)->first();
			if (empty($credentail)) {
				$credentail                          = new MailboxCredentail();
			}

			$credentail->emailbox_mail_driver        = $request->emailbox_mail_driver;
			$credentail->emailbox_mail_host          = $request->emailbox_mail_host;
			$credentail->emailbox_outgoing_port      = $request->emailbox_outgoing_port;
			$credentail->emailbox_incoming_port      = $request->emailbox_incoming_port;
			$credentail->emailbox_mail_username      = $request->emailbox_mail_username;
			$credentail->emailbox_mail_from_address  = $request->emailbox_mail_from_address;
			$credentail->emailbox_mail_password      = $request->emailbox_mail_password;
			$credentail->emailbox_mail_encryption    = $request->emailbox_mail_encryption;
			$credentail->emailbox_mail_from_name     = $request->emailbox_mail_from_name;
			$credentail->workspace_id                = getActiveWorkSpace();
			$credentail->created_by                  = Auth::user()->id;
			$credentail->save();
			return redirect()->route('mailbox.configuration')->with('success', "EMail Box credentail save successfully.");
		} else {
			return redirect()->back()->with('error', __('Permission denied.'));
		}
	}

	public static function SetConfigEmailBoxMail($user_id = null, $workspace_id = null)
	{
		try {
			if (!empty($user_id)) {
				$mail_credentail = MailboxCredentail::where('created_by', $user_id)->first();
			} elseif (!empty($user_id) && !empty($workspace_id)) {
				$mail_credentail = MailboxCredentail::where('created_by', $user_id)->where('workspace_id', $workspace_id)->first();
			} else {
				$mail_credentail = MailboxCredentail::where('workspace_id', getActiveWorkSpace())->where('created_by', Auth::user()->id)->first();
			}

			config(
				[
					'mail.driver' => $mail_credentail['emailbox_mail_driver'],
					'mail.host' => $mail_credentail['emailbox_mail_host'],
					'mail.port' => $mail_credentail['emailbox_outgoing_port'],
					'mail.encryption' => $mail_credentail['emailbox_mail_encryption'],
					'mail.username' => $mail_credentail['emailbox_mail_username'],
					'mail.password' => $mail_credentail['emailbox_mail_password'],
					'mail.from.address' => $mail_credentail['emailbox_mail_from_address'],
					'mail.from.name' => $mail_credentail['emailbox_mail_from_name'],
				]
			);

			return true;
		} catch (\Exception $e) {

			return false;
		}
	}
}
