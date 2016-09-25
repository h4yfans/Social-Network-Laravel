<?php
/**
 * Created by PhpStorm.
 * User: Kaan
 * Date: 9/23/2016
 * Time: 5:37 PM
 */
namespace App\Http\Controllers;




use App\Like;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    public function getDashboard()
    {
        $posts = Post::orderBy('created_at', 'desc')->get();
        return view('dashboard', ['posts' => $posts]);
    }

    public function postCreatePost(Request $request)
    {
        // Validation
        $this->validate($request, [
            'body' => 'required|max:1000'
        ]);

        $post = new Post();
        $post->body = $request['body'];

        $message = 'There was an error :(';
        if ($request->user()->posts()->save($post)) {
            $message = 'Post successfully created! :)';
        }

        return redirect()->route('dashboard')->with(['message' => $message]);
    }

    public function getDeletePost($post_id)
    {
        $post = Post::find($post_id);

        if (Auth::user() != $post->user) {
            return redirect()->back();
        }


        $post->delete();
        return redirect()->route('dashboard')->with(['message' => 'Successfully deleted!']);
    }

    public function postEditPost(Request $request)
    {
        $this->validate($request, [
            'body' => 'required'
        ]);


        $post = Post::find($request['postId']);
        if (Auth::user() != $post->user) {
            return redirect()->back();
        }
        $post->body = $request['body'];
        $post->update();

        return response()->json(['new_body' => $post->body], 200);
    }

    public function postLikePost(Request $request)
    {
        $postID = $request['postId'];
        $isLike = $request['isLike'] === 'true' ? true : false;
        $update = false;
        $post = Post::find($postID);

        if (!$post) {
            return null;
        }

        $user = Auth::user();
        $like = $user->likes()->where('post_id', $postID)->first();

        if ($like) {
            $alreadyLike = $like->like;
            $update = true;
            if ($alreadyLike == $isLike) {
                $like->delete();
                return null;
            }
        }else{
            $like = new Like();
        }
        $like->like = $isLike;
        $like->user_id = $user->id;
        $like->post_id = $post->id;
        if($update){
            $like->update();
        }else{
            $like->save();
        }

        return null;
    }
}
