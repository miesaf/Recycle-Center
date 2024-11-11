@extends('layouts.web')

@section('body')
<main class="app-main">
    <iframe
        width="100%"
        height="100%"
        style="border:0"
        loading="lazy"
        allowfullscreen
        referrerpolicy="no-referrer-when-downgrade"
        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyA3MUZnhGu_x_up9sodns4Wcb5YIf91VSs
        &q=UiTM+Shah+Alam">
    </iframe>

    <!-- prettier-ignore -->
    <script>
        (
            g=>{
                var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});
                var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));
                e.set("libraries",[...r]+"");
                for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);
                e.set("callback",c+".maps."+q);
                a.src=`https://maps.${c}apis.com/maps/api/js?`+e;
                d[q]=f;
                a.onerror=()=>h=n(Error(p+" could not load."));
                a.nonce=m.querySelector("script[nonce]")?.nonce||"";
                m.head.append(a)}));
                d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))
            }
        )
        (
            {
                key: "AIzaSyA3MUZnhGu_x_up9sodns4Wcb5YIf91VSs",
                v: "weekly"
            }
        );
    </script>
</main>
@endsection