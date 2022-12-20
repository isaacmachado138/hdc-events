<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; //usado para pegar dados de um form

use App\Models\Event; //usa o model de event

use App\Models\User; //usa o model de user


class EventController extends Controller
{
    
    public function index() {

        $search = request('search'); //recebe o valor inserido no buscar
        if($search){

            $events = Event::where([
                ['title', 'like' , '%'.$search."%"] //um array com a condicao do where
            ])->get(); //precisa colocar esse get no final para receber o resultado

        }else{
            $events = Event::all(); //pega todos os eventos do banco
        }
    
        return view('welcome', ['events' => $events, 'search' => $search]);
    }

    public function create() {
        return view('events.create');
    }

    public function store(Request $request){
        //estancia o evento
        $event = new Event;
        //campos do evento recebem campos preenchidos no form, que vem do request
        $event->title       = $request->title;
        $event->date       = $request->date;
        $event->city        = $request->city;
        $event->private     = $request->private; 
        $event->description = $request->description;

        //recebe o array da lista de items
        //antes disso foi feito um cast em event para mostrar que items e um array
        $event->items       = $request->items;

        //Image upload
        if($request->hasfile('image') && $request->file('image')->isValid()){

            $requestImage = $request->image;

            $extension = $requestImage->extension();

            //nome da imagem no banco apenas
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now") . "." . $extension); 

            $requestImage->move(public_path("img/events"), $imageName);//copia a imagem

            $event->image = $imageName;
        }

        $user = auth()->user(); //retorna informacoes do usuario logado
        $event->user_id = $user->id; //camo user id do eventos recebe o id do usuario

        $event->save(); //metodo para fazer o insert no banco

        return redirect("/")->with('msg', 'Evento criado com sucesso!'); //cria a mensagem após criar o eventos
    }

    public function show($id) {
        $event = Event::findOrFail($id); //find or fail busca o registro no banco pelo id, se nao achar da erro 404

        $user = auth()->user();
        $hasUserJoined = false;

        if($user) {
            $userEvents = $user->eventsAsParticipant->toArray();

            foreach($userEvents as $userEvent) {
                if($userEvent['id'] == $id) {
                    $hasUserJoined = true;
                }
            }
        }

        // var = model de user chamando um where user=user_id=>primeiro que encontrar()->transformando em array
        $eventOwner = User::where('id', $event->user_id)->first()->toArray();

    //view exibindo o evento
    return view('events.show', ['event' => $event, 'eventOwner' => $eventOwner, 'hasUserJoined' => $hasUserJoined]); 
    }

    public function dashboard(){
        $user = auth()->user();

        $events = $user->events;

        //user chamará o metodo criado no model user, que é usado para ligar usuario ao evento
        $eventsAsParticipant = $user->eventsAsParticipant; //acesso aos eventos que o usuario participa

        return view('events.dashboard',
        ['events' => $events, 'eventsAsParticipant' => $eventsAsParticipant]);
    }

    public function destroy($id){

        Event::findOrFail($id)->delete();

        return redirect('/dashboard')->with('msg', 'Evento excluído com sucesso!');
    }

    public function edit($id){

        $user = auth()->user(); //pega informacoes do usuario logado

        $event = Event::findOrFail($id);

        if($user->id != $event->user->id){
            return redirect('/dashboard');
        }

        return view('events.edit', ['event' => $event]);
    }

    public function update(Request $request){ //recebe o request com as informações da pag de edit

        $data = $request->all(); //data recebe todas as informações que vieram do request

        // Image Upload
        if($request->hasFile('image') && $request->file('image')->isValid()) {

            $requestImage = $request->image;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

            $requestImage->move(public_path('img/events'), $imageName);

            $data['image'] = $imageName;

        }

        //encontra o evento, pega o id por request e faz o update da variavel que contem tudo que veio do request
        Event::findOrFail($request->id)->update($data);
        
        //redireciona para a dashboard com a mensagem de editado com sucesso
        return redirect('/dashboard')->with('msg', 'Evento editado com sucesso!');
    }

    public function joinEvent($id){ //receberá o id do evento

        $user = auth()->user(); //pega informacoes do usuario logado

        //user chamará o metodo criado no model user, que é usado para ligar usuario ao evento
        $user->eventsAsParticipant()->attach($id); //attach fará isso e construirá a linha

        $event = Event::findOrFail($id);
        return redirect('/dashboard')->with('msg', 'Presença confirmada no evento ' . $event->title . '!');
    }

    public function leaveEvent($id) {

        $user = auth()->user(); //pega informacoes do usuario logado

        $user->eventsAsParticipant()->detach($id); //detach: remove a ligação entre usuario e evento na tabela

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Você saiu com sucesso do evento: ' . $event->title);

    }
}



