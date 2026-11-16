<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return BookResource::collection($request->user()->books()->latest('id')->paginate(10));
    }

    public function show(Request $request, int $book): BookResource
    {
        return new BookResource($this->ownedBook($request, $book));
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $request->user()->books()->create($request->validated());

        return (new BookResource($book))->response()->setStatusCode(201);
    }

    public function update(UpdateBookRequest $request, int $book): BookResource
    {
        $model = $this->ownedBook($request, $book);
        $model->update($request->validated());

        return new BookResource($model);
    }

    public function destroy(Request $request, int $book): JsonResponse
    {
        $this->ownedBook($request, $book)->delete();

        return response()->json(['message' => 'Book deleted.']);
    }

    // Only searches the user's own books, so someone else's book gives 404
    private function ownedBook(Request $request, int $id): Book
    {
        return $request->user()->books()->findOrFail($id);
    }
}