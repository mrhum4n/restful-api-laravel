<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Contracts\Database\Eloquent\Builder;
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

    public function update(int $id, ContactUpdateRequest $request): ContactResource {
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

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    public function detele(int $id): JsonResponse {
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

        $contact->delete();

        return response()->json([
            'error' => false,
            'message' => 'contact has been deleted!'
        ])->setStatusCode(200);
    }

    public function search(Request $request): ContactCollection {
        $userId = Auth::user()->id;
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $contacts = Contact::query()->where('user_id', $userId);
        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            $email = $request->input('email');
            $phone = $request->input('phone');

            if ($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere('first_name', 'like' , '%' . $name . '%');
                    $builder->orWhere('last_name', 'like' , '%' . $name . '%');
                });
            }

            if ($email) {
                $builder->where('email', 'like', '%' . $email . '%');
            }

            if ($phone) {
                $builder->where('phone', 'like', '%' . $phone . '%');
            }
        });
        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return new ContactCollection($contacts);
    }
}
