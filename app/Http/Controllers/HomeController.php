<?php

namespace App\Http\Controllers;

use App\LimanRequest;
use App\Server;
use App\Widget;
use function MongoDB\generate_index_name;
use mysql_xdevapi\Session;
use Twig_Sandbox_SecurityPolicy;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $widgets = Widget::where('user_id',auth()->id())->get();
        foreach($widgets as $widget){
            $widget->server_name = Server::where('_id',$widget->server_id)->first()->name;
        }
        return view('index',[
            "widgets" => $widgets
        ]);
    }

    public function setLocale(){
        $languages = ["tr","en"];
        if(request()->has('locale') && in_array(request('locale'),$languages)){
            \Session::put('locale', request('locale'));
            return response('Alright',200);
        }else{
            return response('Language not found',404);
        }
    }

    public function collapse()
    {
        if(\Session::has('collapse')){
            \Session::remove('collapse');
        }else{
            \Session::put('collapse','');
        }
        return respond('Ok',200);
    }

    public function setTheme(){
        if(\Session::has('dark_mode')){
            \Session::remove('dark_mode');
        }else{
            \Session::put('dark_mode','true');
        }
        return respond('Tema Guncellendi.');
    }

    public function new(){
        return view('permission.request');
    }

    public function all(){
        $requests = LimanRequest::where('user_id',auth()->id())->get();
        foreach ($requests as $request){
            switch ($request->status){
                case "0":
                    $request->status = __("Talep Alındı");
                    break;
                case "1":
                    $request->status = __("İşleniyor");
                    break;
                case "2":
                    $request->status = __("Tamamlandı.");
                    break;
                case "3":
                    $request->status = __("Reddedildi.");
                    break;
                default:
                    $request->status = __("Bilinmeyen.");
                    break;
            }
        }
        return view('permission.all',[
            "requests" => $requests
        ]);
    }

    public function request(){
        $req = new LimanRequest();
        $req->user_id = \Auth::id();
        $req->email = \Auth::user()->email;
        $req->note = request('note');
        $req->type = request('type');
        $req->speed = request('speed');
        $req->status = 0;
        $req->save();
        return respond('Talebiniz başarıyla alındı.',200);
    }

    public function deneme(){
        $loader = new \Twig_Loader_Filesystem(app_path() . '/../resources/views/twig');
        $twig = new \Twig_Environment($loader,["auto_reload" => true,"debug" => true]);
        $tags = ['if'];
        $filters = ['upper','for'];
        $methods = [
            'Article' => ['getTitle', 'getBody'],
        ];
        $properties = [
            'Article' => ['title', 'body'],
        ];
        $functions = ['echo','for'];

        $policy = new Twig_Sandbox_SecurityPolicy($tags, $filters, $methods, $properties, $functions);

        $sandbox = new \Twig_Extension_Sandbox($policy,true);
        $twig->addExtension($sandbox);

        return $twig->render('deneme.twig',[
            "mert" => "celen",
            "server" => servers()
        ]);
    }
}