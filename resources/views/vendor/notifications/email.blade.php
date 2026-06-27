<x-mail::message>
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('Hello!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Regards,')<br>

@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>

<p style="font-size: 10px;color:#cccccc;">Posta elektroniko hau eta, hala badagokio, honekin batera doan edozein eranskin horren hartzeileari zein
hartzaileei bakarrik zuzendutako isilpeko informazioa dauka. Interneten bidezko posta elektronikoak ez du
konfidentzialtasunik ez osotasunik segurtatzen, ezta zuzen jasotzea ere. Debekatuta dago horren informazioa
zabaltzea, kopiatzea zein beste batzuen artean banatzea ETSek idatziz horretarako baimena eman ezean. Posta
elektroniko hau errakuntzagatik jaso baduzu, egoera honen berri eman dezazula arren eskatu nahi dizugu
berriz ere igorlearen helbide elektronikora bidaliz.</p>

<p style="font-size: 10px;color:#cccccc;">Este correo electronico y, en su caso, cualquier fichero anexo al mismo, contiene informacion de caracter
confidencial exclusivamente dirigida a su destinatario/a o destinatarios/as. El correo electronico via
Internet no permite asegurar la confidencialidad de los mensajes que se transmiten ni su integridad o
correcta recepcion. Queda prohibida su divulgacion, copia o distribucion a terceros sin la previa
autorizacion escrita de ETS. En el caso de haber recibido este correo electronico por error, se ruega
notificar inmediatamente esta circunstancia mediante reenvio a la direccion electronica del remitente.</p>
</x-slot:subcopy>
@endisset
</x-mail::message>


  {{-- <span class="break-all">
                # Posta elektroniko hau eta, hala badagokio, honekin batera doan edozein eranskin horren hartzeileari zein
                hartzaileei bakarrik zuzendutako isilpeko informazioa dauka. Interneten bidezko posta elektronikoak ez du
                konfidentzialtasunik ez osotasunik segurtatzen, ezta zuzen jasotzea ere. Debekatuta dago horren informazioa
                zabaltzea, kopiatzea zein beste batzuen artean banatzea ETSek idatziz horretarako baimena eman ezean. Posta
                elektroniko hau errakuntzagatik jaso baduzu, egoera honen berri eman dezazula arren eskatu nahi dizugu
                berriz ere igorlearen helbide elektronikora bidaliz.

                # Este correo electronico y, en su caso, cualquier fichero anexo al mismo, contiene informacion de caracter
                confidencial exclusivamente dirigida a su destinatario/a o destinatarios/as. El correo electronico via
                Internet no permite asegurar la confidencialidad de los mensajes que se transmiten ni su integridad o
                correcta recepcion. Queda prohibida su divulgacion, copia o distribucion a terceros sin la previa
                autorizacion escrita de ETS. En el caso de haber recibido este correo electronico por error, se ruega
                notificar inmediatamente esta circunstancia mediante reenvio a la direccion electronica del remitente.
            </span> --}}
