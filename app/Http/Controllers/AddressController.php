<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AddressResource;
use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Models\Address;
use App\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressController extends Controller
{
    private function getContact(User $user, int $idContact): Contact {
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'data not found!'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $contact;
    }

    private function getAddress(Contact $contact, int $idAddress): Address {
        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'data not found!'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $address;
    }

    public function create(int $idContact, AddressCreateRequest $request): JsonResponse {
        // ambil user id yang sedang login
        $user = Auth::user();
        
        // cari contact berdasarkan user id & contact id
        $contact = $this->getContact($user, $idContact);

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): AddressResource {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete(int $idContact, int $idAddress): JsonResponse {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);
        $address->delete();

        return response()->json([
            'error' => false,
            'message' => 'contact has been deleted!'
        ])->setStatusCode(200);
    }

    public function getAll(int $idContact): JsonResponse {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $addresses = Address::where('contact_id', $contact->id)->get();
        return (AddressResource::collection($addresses))->response()->setStatusCode(200);
    }
}
