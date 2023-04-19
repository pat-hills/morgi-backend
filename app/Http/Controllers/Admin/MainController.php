<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\ChatBadWord;
use App\Models\ContentEditor;
use App\Models\RookieWinnerHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MainController extends Controller
{
    private $users_table = 'users';
    private $rookies_table = 'rookies';
    private $chat_bad_words_table = 'chat_bad_words';
    private $content_editor_table = 'content_editors';
    private $rookie_winner_history_table = 'rookies_winners_histories';

    public function loginPage(){

        if (empty(session('user'))) {
            return view('admin-login');
        }

        return redirect(route('index'));
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(), [

            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string']

        ]);

        if ($validator->fails()) {
            return redirect("/login")->withErrors($validator)->withInput();
        }


        $credentials = $request->only('email', 'password');
        $user = User::query()->where('email', $request->email)->first();

        if (!$user) {
            return redirect("/login")->with(['fail' => trans('auth.wrong_credentials')])->withInput();
        }

        if (!in_array($user->type, ['admin', 'operator'])) {
            return redirect('/login')->with(['fail' => trans('auth.wrong_credentials')])->withInput();
        }

        if (!Auth::attempt($credentials)) {
            return redirect('/login')->with(['fail' => trans('auth.wrong_credentials')])->withInput();
        }

        $request->session()->put('user', $user);
        return redirect("/");
    }

    public function logout(){
        session()->flush();
        return redirect("/login");
    }

    public function homePage(){
        return view('admin-home');
    }

    public function getBadWords()
    {

        $bad_words = ChatBadWord::all();

        return view('admin.admin-pages.bad-words', compact('bad_words'));
    }

    public function addBadWord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $bad_words = ChatBadWord::all();

        foreach ($bad_words as $word){
            if(strtolower($word->name) == strtolower($request->name)){
                return redirect()->back()->with(['fail' => "Bad word: $word->name already added id: $word->id"]);
            }
        }

        ChatBadWord::create([
            'name' => $request->name
        ]);

        return \redirect()->back()->with(['success' => 'Word Added']);
    }

    public function deleteBadWord(Request $request){

        $validator = Validator::make($request->all(), [
            'word_id' => "required|integer|exists:$this->chat_bad_words_table,id"
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fails' => $validator->errors()->getMessages()]);
        }

        $word = ChatBadWord::query()->find($request->word_id);
        $word_name = $word->name;
        $word->delete();

        return redirect()->back()->with(['success' => "$word_name successfully deleted"]);
    }

    public function indexContentEditor(Request $request){

        $old_input = null;

        $query = ContentEditor::query();
        if($request->has('search_type') || $request->has('search_text')){
            $validator = Validator::make($request->all(), [
                'search_type' => 'in:inspiration,news_update|nullable',
                'search_text' => 'nullable'
            ]);
            if($validator->fails()){
                return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
            }


            if ($request->has('search_text') && !empty($request->search_text)){
                $query->where(function ($query_text) use ($request){
                    $query_text->orWhere('title', 'LIKE', '%'. $request->search_text .'%')
                        ->orWhere('content', 'LIKE', '%'. $request->search_text .'%');
                });
            }

            if ($request->has('search_type') && !is_null($request->search_type)){
                $query->where("$this->content_editor_table.type", "$request->search_type");
            }

            $old_input = $request->all();

        }

        $query->join($this->users_table, "$this->content_editor_table.admin_id", '=', "$this->users_table.id");

        $select =[
            "$this->content_editor_table.*",
            "$this->users_table.username as admin_username",
            "$this->users_table.email as admin_email"
        ];

        $contents = $query->select($select)->orderBy('updated_at', 'DESC')->get();


        return view('admin.admin-pages.content_editor.index', compact('contents', 'old_input'));
    }

    public function createContentEditor(Request $request): \Illuminate\Http\RedirectResponse
    {

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:inspiration,news_update',
            'content_text' => 'required',
            'title' => 'string|nullable'
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        try {
            ContentEditor::query()->create([
                'admin_id' => Auth::id(),
                'title' => $request->title,
                'type' => $request->type,
                'content' => $request->content_text,
            ]);

            Cache::tags('content_editor')->flush();

        }catch (\Exception $exception){
            return \redirect()->back()->with(['fail' => 'Something went wrong with the creation of Content Editor']);
        }
        return \redirect()->back()->with(['success' => 'Content created!']);
    }

    public function updateContentEditor(Request $request): \Illuminate\Http\RedirectResponse
    {

        $validator = Validator::make($request->all(), [
            'contentID' => 'required|integer|exists:'. (new ContentEditor())->getTable().',id',
            'editType' => 'required|in:inspiration,news_update',
            'editTitle' => 'required',
            'editContent' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $content = ContentEditor::query()->where('id', $request->contentID)->first();

        $content->update([
            'type' => $request->editType,
            'title' => $request->editTitle,
            'content' => $request->editContent
        ]);

        Cache::tags('content_editor')->flush();

        return \redirect()->back()->with(['success' => 'Content Updated']);
    }

    public function deleteContentEditor(Request $request){

        $validator = Validator::make($request->all(), [
            'contentID' => "required|integer|exists:$this->content_editor_table,id",
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        ContentEditor::query()->where('id', $request->contentID)->delete();

        Cache::tags('content_editor')->flush();

        return \redirect()->back()->with(['success' => 'Content Deleted']);
    }

    public function showRookieOfTheDay(Request $request){

        $validator = Validator::make($request->all(), [
            'from_date' => 'date|nullable',
            'to_date' => 'date|nullable|after_or_equal:from_date',
            'page' => 'int|nullable',
            'by_first_name' => 'string|nullable',
            'by_last_name' => 'string|nullable'
        ]);

        if($validator->fails()){
            return \redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $url_query = [];

        $query = RookieWinnerHistory::query();

        $query->orderBy("$this->rookie_winner_history_table.win_at", 'DESC');

        $page = $request->has('page') ? $request->get('page') : 1;
        $url_query['page'] = $page;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        $url_query['limit'] = $limit;

        $query->join($this->rookies_table, "$this->rookie_winner_history_table.rookie_id", '=', "$this->rookies_table.id");

        $latest = User::query()
            ->join($this->rookie_winner_history_table, "$this->users_table.id", "=", "$this->rookie_winner_history_table.rookie_id")
            ->latest("$this->rookie_winner_history_table.win_at")
            ->limit(3)
            ->get();

        $rookies_query = clone $query;

        if($request->has('from_date') && !empty($request->from_date)){
            $rookies_query->where('win_at', '>=', $request->from_date);
            $url_query['from_date'] = $request->from_date;
        }

        if($request->has('to_date') && !empty($request->to_date)){
            $rookies_query->where('win_at', '<=', $request->to_date);
            $url_query['to_date'] = $request->to_date;
        }

        if($request->has('by_first_name') && !empty(($request->by_first_name))){
            $rookies_query->where("$this->rookies_table.first_name", 'LIKE', '%'. $request->by_first_name .'%');
            $url_query['by_first_name'] = $request->by_first_name;
        }

        if($request->has('by_last_name') && !empty(($request->by_last_name))){
            $rookies_query->where("$this->rookies_table.last_name", 'LIKE', '%'. $request->by_last_name .'%');
            $url_query['by_last_name'] = $request->by_last_name;
        }

        $rows = $rookies_query->get();

        $rookies_query->limit($limit*3)->offset(($page - 1) * ($limit*3));

        $rookies = $rookies_query->get();

        $pages = round(count($rows)/($limit*3));


        $winners = [];

        foreach ($rookies as $rookie){
            if(!array_key_exists(date('Y-M-d', strtotime($rookie->win_at)), $winners)){
                $winners[date('Y-M-d', strtotime($rookie->win_at))] = [];
            }
        }

        foreach ($winners as $key => $winner){
            foreach ($rookies as $rookie){
                if($key == date('Y-M-d', strtotime($rookie->win_at))){
                    $winners[$key][] = $rookie;
                }
            }
        }

        $query = route('show.rookies_ofd'). '?'.http_build_query($url_query);

        return view('admin.admin-pages.rookie_winners.index', compact('rookies', 'latest', 'winners', 'pages', 'limit', 'query', 'request'));
    }


    public function checkUsername(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => ['string', 'unique:users,username'],
        ]);

        if($validator->fails()){
            throw ValidationException::withMessages([$validator->errors()->first()]);
        }

        return response()->json(['status' => 200, 'message' => 'ok']);
    }

}
