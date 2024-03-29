@extends('auth.auth')
@section('content')

<link href="{{asset('developer/admin/css/developer.css')}}" rel="stylesheet">
<div class="login-logo">
        <a href="/"><strong style="color: white;">Reset Password</strong></a>
    </div>
<div class="login">
    
    <img src="{{asset('img/idle/1.png')}}" id="monster" alt="">
    <form  method="POST" action="{{ route('user.resetpasswordupdate',$validateString) }}" id="resetPassword" class="formulario">
        @csrf
       
        <label>Password</label>
        
        <input  id="input-clave" type="password" class="form-control" name="password" placeholder="Password">
        {{$errors->first('password') ?? ''}}

        <label>Confirm Password</label>
        <input  type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" value="{{ old('password_confirmation') }}"  autocomplete="new-password">
        {{$errors->first('password_confirmation') ?? ''}}

 
      
        <button type="submit">Login</button>
       
        @include('alert::alert')
    </form>
</div>

@parent
<script>
    const monster = document.getElementById('monster');
    const inputUsuario = document.getElementById('input-usuario');
    const inputClave = document.getElementById('input-clave');
    const body = document.querySelector('body');
    const anchoMitad = window.innerWidth / 2;
    const altoMitad = window.innerHeight / 2;
    let seguirPunteroMouse = true;
    
    body.addEventListener('mousemove', (m) => {
        if (seguirPunteroMouse) {
            if (m.clientX < anchoMitad && m.clientY < altoMitad) {
                monster.src = "{{asset('img/idle/2.png')}}";
            } else if (m.clientX < anchoMitad && m.clientY > altoMitad) {
                monster.src = "{{asset('img/idle/3.png')}}";
            } else if (m.clientX > anchoMitad && m.clientY < altoMitad) {
                monster.src = "{{asset('img/idle/5.png')}}";
            } else {
                monster.src = "{{asset('img/idle/4.png')}}";
            }
        }
    })
    
    inputUsuario.addEventListener('focus',()=>{
        seguirPunteroMouse = false;
    })
    
    inputUsuario.addEventListener('blur',()=>{
        seguirPunteroMouse = true;
    })
    
    inputUsuario.addEventListener('keyup',()=>{
        let usuario = inputUsuario.value.length;
        if(usuario >= 0 && usuario<=5){
            monster.src = "{{asset('img/read/1.png')}}";
        }else if(usuario >= 6 && usuario<=14){
            monster.src = "{{asset('img/read/2.png')}}";
        }else if(usuario >= 15 && usuario<=20){
            monster.src = "{{asset('img/read/3.png')}}";
        }else{
            monster.src = "{{asset('img/read/4.png')}}";
        }
    })
    
    inputClave.addEventListener('focus',()=>{
        seguirPunteroMouse = false;
        let cont = 1;
        const cubrirOjo = setInterval(() => {
            monster.src = 'img/cover/'+cont+'.png';
            if(cont < 8){
                cont++;
            }else{
                clearInterval(cubrirOjo);
            }
        }, 60);
    })
    
    inputClave.addEventListener('blur',()=>{
        seguirPunteroMouse = true;
        let cont = 7;
        const descubrirOjo = setInterval(() => {
            monster.src = 'img/cover/'+cont+'.png';
            if(cont > 1){
                cont--;
            }else{
                clearInterval(descubrirOjo);
            }
        }, 60);
    })
    
    
    </script>
    
@endparent
@endsection
    






