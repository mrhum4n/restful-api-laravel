<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse {
        $data = $request->validated();
        // mengambil user id yang sedang login
        $userId = Auth::user()->id;

        $contact = new Contact($data);
        $contact->user_id = $userId;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $id): ContactResource {
        $userId = Auth::user()->id;
        $contact = Contact::where('id', $id)->where('user_id', $userId)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'contact not found!'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new ContactResource($contact);
    }
}
