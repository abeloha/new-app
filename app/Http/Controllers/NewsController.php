<?php

namespace App\Http\Controllers;

use App\Events\NewsCreated;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    const NUMBER_OF_RECORDS_PER_PAGE = 5;

    /**
     * Display a listing of the news, tatest news first.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return NewsResource::collection(News::with('user')->latest()->paginate(self::NUMBER_OF_RECORDS_PER_PAGE));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreNewsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNewsRequest $request)
    {

        try {

            //inject user_id
            $record = $request->validated();
            $record['user_id'] = $request->user()->id;
            $news = News::create($record);

            //dispatch event
            NewsCreated::dispatch($news);

            return new NewsResource($news);

        } catch(QueryException $e){
            //db constraints may fail

            $errorCode = $e->errorInfo[1];
            $data = [
                'errors' => [
                    'message' =>  "Error creating record. Try again later. (ErrorCode: {$errorCode})",
                 ]
            ];

            return $this->appResponse('Failed', $data);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function show(News $news)
    {
        //load user
        $news->user;
        return New NewsResource($news);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function edit(News $news)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateNewsRequest  $request
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNewsRequest $request, News $news)
    {
        $this->authorize('update', $news);

        $news->update(
            $record = $request->validated(),
        );

        return New NewsResource($news);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function destroy(News $news)
    {
        $this->authorize('delete', $news);

        $news->delete();
        return response(null, 204);
    }
}
