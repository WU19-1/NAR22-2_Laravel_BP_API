<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function all(){
        $arr = Book::all(DB::raw("CONCAT(id, ';', title) AS titles"))->pluck('titles')->toArray();
        return response(['data' => $arr], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $total = $request->total;
        $page = $request->page;
        if ($total == null) $total = 8;
        if ($page == null) $page = 1;
        $books = Book::where('title', 'LIKE', '%' . $request->q . '%')
            ->paginate($total, ['*'], 'page', $page);
        if ($books->total() == 0){
            return response(['message' => 'not found'], 404);
        } else {
            return response(['data' => $books], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all() ,[
            'title' => 'required',
            'author' => 'required',
            'language' => 'required',
            'cover' => 'required',
            'genre' => 'required',
            'publication_date' => 'required|date',
            'publisher' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()){
            return response(['message' => $validator->errors()->first()], 422);
        }
        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'language' => $request->language,
            'cover' => $request->cover,
            'genre' => $request->genre,
            'publication_date' => $request->publication_date,
            'publisher' => $request->publisher,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => 0
        ]);
        return response(['message' => 'successfully added book'],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::find($id);
        if ($book == null)
            return response(['message' => 'book not found'], 404);
        else
            return response(['data' => $book], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->json()->all();
        $validator = Validator::make($data ,[
            'title' => 'sometimes|string',
            'author' => 'sometimes|string',
            'language' => 'sometimes|string',
            'cover' => 'sometimes|string',
            'genre' => 'sometimes|string',
            'publication_date' => 'sometimes|date',
            'publisher' => 'sometimes|string',
            'description' => 'sometimes|string',
            'stock' => 'sometimes|numeric'
        ]);
        if ($validator->fails()){
            return response(['message' => $validator->errors()->first()], 422);
        }
        $book = Book::find($id);
        $book->fill($request->all())->save();
        return response(['message' => 'successfully update book!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = $request->json()->all();
        $book = Book::find($data['id']);
        if ($book == null) return response(['message' => 'book not found'], 404);
        $book->delete();
        return response(['message' => 'book successfully deleted'], 404);
    }
}
