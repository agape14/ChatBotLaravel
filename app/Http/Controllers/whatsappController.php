<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;
use App\Models\WhatsappInteraction;
use App\Jobs\SendInactivityMessage;

use App\Models\ChatbotUsuario;
use App\Models\ChatbotMensaje;
use Carbon\Carbon;

class whatsappController extends Controller
{

  public $ws_token;
  public $ws_endpoint;
  public $ws_tokenwebhook;

  public function __construct()
  {
      $this->ws_token = env('WHATSAPP_TOKEN');
      $this->ws_endpoint = env('ENDPOINT_RPTA');
      $this->ws_tokenwebhook = env('TOKEN_WEBHOOK');
  }

  function ws_responder_texto($numero, $body)
  {
    return [
      'headers' => [
        'Content-Type' => 'application/json', // Tipo de contenido
        'Authorization' => 'Bearer ' . $this->ws_token,  // Incluye el token de autorización
      ],
      'json' => [
        'messaging_product' => 'whatsapp',
        'to' => $numero,  // Número de destino
        'type' => 'text', // Tipo de mensaje
        'text' => [
          'body' => $body, // Cuerpo, en este caso texto
        ],
      ]
    ];
  }

  function ws_responder_ubicacion($numero, $latitud, $longitud)
  {

    return  [
      'headers' => [
        'Authorization' => 'Bearer ' . $this->ws_token,  // Incluye el token de autorización
        'Content-Type' => 'application/json',  // Tipo de contenido
      ],
      'json' => [
        'messaging_product' => 'whatsapp',
        'to' => $numero,  // Número de destino
        'type' => 'location',  // Tipo de mensaje
        'location' => [
          'latitude' => $latitud,  // Latitud de la ubicación
          'longitude' => $longitud,  // Longitud de la ubicación
          'name' => 'Ubicación de Angel Geraldo Tech',  // Nombre de la ubicación (opcional)
          'address' => 'Dirección de ejemplo',  // Dirección (opcional)
        ],
      ]
    ];
  }

  function ws_responder_pdf($numero, $pdf_url)
  {
      return [
          'headers' => [
              'Authorization' => 'Bearer ' . $this->ws_token, // Token de autorización
              'Content-Type' => 'application/json', // Tipo de contenido
          ],
          'json' => [
              'messaging_product' => 'whatsapp',
              'to' => $numero, // Número de destino
              'type' => 'document', // Tipo de mensaje (documento)
              'document' => [
                  'link' => $pdf_url, // URL del PDF
                  'caption' => 'Aquí tienes el documento solicitado.', // Mensaje opcional
                  'filename' => 'VICIdial_White-Paper_20250130.pdf' // Nombre del archivo opcional
              ],
          ]
      ];
  }

  function ws_responder_audio($numero, $audio_url)
  {
      return [
          'headers' => [
              'Authorization' => 'Bearer ' . $this->ws_token, // Token de autorización
              'Content-Type' => 'application/json', // Tipo de contenido
          ],
          'json' => [
              'messaging_product' => 'whatsapp',
              'to' => $numero, // Número de destino
              'type' => 'audio', // Tipo de mensaje (audio)
              'audio' => [
                  'link' => $audio_url // URL del archivo de audio
              ],
          ]
      ];
  }

  function ws_responder_video_youtube($numero, $youtube_url)
  {
      return [
          'headers' => [
              'Authorization' => 'Bearer ' . $this->ws_token, // Token de autorización
              'Content-Type' => 'application/json', // Tipo de contenido
          ],
          'json' => [
              'messaging_product' => 'whatsapp',
              'to' => $numero, // Número de destino
              'type' => 'text', // Tipo de mensaje (texto con enlace)
              'text' => [
                  'preview_url' => true, // Habilita la previsualización del enlace
                  'body' => $youtube_url // Enlace de YouTube
              ],
          ]
      ];
  }

  // Bono!
  function ws_responder_botones($numero)
  {
      return [
          'headers' => [
              'Authorization' => 'Bearer ' . $this->ws_token, // Token de autorización
              'Content-Type' => 'application/json', // Tipo de contenido
          ],
          'json' => [
              'messaging_product' => 'whatsapp',
              'to' => $numero, // Número de destino
              'type' => 'interactive', // Tipo de mensaje interactivo
              'interactive' => [
                  'type' => 'button', // Tipo de interacción
                  'body' => [
                      'text' => 'Selecciona una opción:' // Texto del mensaje
                  ],
                  'action' => [
                      'buttons' => [
                          [
                              'type' => 'reply',
                              'reply' => [
                                  'id' => 'registro',
                                  'title' => 'Registrarse'
                              ]
                          ],
                          [
                              'type' => 'reply',
                              'reply' => [
                                  'id' => 'ver_balance',
                                  'title' => 'Ver Balance'
                              ]
                          ],
                          [
                              'type' => 'reply',
                              'reply' => [
                                  'id' => 'realizar_pago',
                                  'title' => 'Realizar Pago'
                              ]
                          ]
                      ]
                  ]
              ]
          ]
      ];
  }


    function enviarRespuesta($comentario, $numero, $id, $timestamp, $from)
    {
        $endpoint = $this->ws_endpoint;
        $comentario = Str::lower(trim($comentario));
        // Registrar/actualizar la interacción
        $interaction = WhatsappInteraction::updateOrCreate(
            ['phone_number' => $numero],
            ['last_interaction' => now(), 'auto_message_sent' => false]
        );

        // Programar job para verificar inactividad
        SendInactivityMessage::dispatch($numero)
        ->delay(now()->addMinutes(5));

        $opciones = [
            '1' => <<<TXT
            📌 Tenemos 7 terrenos disponibles en 5 distritos de Lima Metropolitana.
            Cada uno cuenta con distintas oportunidades de inversión gracias a su excelente ubicación.

            Revisa el listado completo y todos los detalles, como dimensiones, precio base, partida registral, entre otros en este enlace: 🔗[https://emilima.com.pe/Subastas/CATALOGO_SUBASTA_2025_segunda_convocatoria.pdf]
            TXT,

            '2' => <<<TXT
            Para participar en la subasta, sigue estos pasos:

            1️⃣ Compra tus bases a S/50.00
            Presencial: Pago en el Banco de Crédito (Cuenta Corriente N° 193-11271150-99 a nombre de EMILIMA S.A.) y presentación del comprobante en la Subgerencia de Tesorería.
            Virtual: A través de la página web www.emilima.com.pe/home.

            2️⃣ Depósito de garantía
            Depósito bancario al N° Cuenta Corriente Soles: 191-4217528-0-91 con N° Código de Cuenta Interbancaria: 00219100421752809158, de EMILIMA - FOMUR, remitido al correo subasta@emilima.com.pe, indicando datos completos y el lote a postular, a fin de verificar y brindarle el recibo.
            Cheque de Gerencia No Negociable a nombre de EMILIMA - FOMUR, por el (los) predio(s) a los que postule, presentándose a la Subgerencia de Tesorería.

            3️⃣ Inscripción
            Presencial: Jr. Cuzco N° 286, Cercado de Lima (mesa de partes).
            Virtual: https://sgd.emilima.com.pe/mesapartesvirtual.html.

            📌 Inscripciones hasta el viernes 20 de junio. Para más detalles, revisa: https://beacons.ai/emilima.sa
            TXT,

          '3' => <<<TXT
            📋 Requisitos para participar:

            📌 Para personas naturales:
            * Anexo 03 de las Bases ([Descargar PDF] https://emilima.com.pe/Subastas/anexo_03_bases.pdf )
            * Declaración Jurada de procedencia lícita de fondos ([Descargar PDF] https://emilima.com.pe/Subastas/declaracion_procedencia_licita_fondos_2025.pdf )
            * Copia de DNI.
            * Comprobante de compra de bases emitido por EMILIMA S.A.
            * Recibo de caja por concepto de garantía emitido por EMILIMA S.A.

            📌 Para personas jurídicas:
            * Anexo 03 de las Bases ([Descargar PDF] https://emilima.com.pe/Subastas/anexo_03_bases.pdf )
            * Declaración Jurada de procedencia lícita de fondos ([Descargar PDF] https://emilima.com.pe/Subastas/declaracion_procedencia_licita_fondos_2025.pdf )
            * Copia de DNI.
            * Copia de RUC y Vigencia de poder del representante legal.
            * Comprobante de compra de bases emitido por EMILIMA S.A.
            * Recibo de caja por concepto de garantía emitido por EMILIMA S.A.

            📆 Fecha de la subasta: Lunes 23 de junio 2025
            📍 Lugar: Museo Metropolitano de Lima (Sala Taulichusco), Av. 28 de julio con Av. Garcilaso de la Vega – Parque de la Exposición, Cercado de Lima
            ⏰ Hora: 11:00 a.m.
            🔹 Modalidad: Mixta (presencial y virtual para postores fuera de Lima Metropolitana)
            TXT,

            '4' => <<<TXT
            Actualmente, EMILIMA ha puesto a disposición 7 espacios comerciales para arrendamiento público en las siguientes zonas:

            📍 Parque de la Exposición
            Módulos comerciales, módulos de SS.HH. y patio de comidas
            Áreas desde 38.36 m² hasta 213.42 m²
            Renta base mensual desde S/ 3,278.70 hasta los S/12,429.00

            📍 Cercado de Lima
            Contamos con un módulo de SS.HH.
            Área: 25.57 m²
            Renta base mensual: S/ 452.60

            🔗 Puedes ver el listado completo y detallado en el siguiente enlace:
            👉 [https://emilima.com.pe/Subastas/catalogo_arrendamiento_segunda_convocatoria_2025.pdf]
            TXT,
            '5' => <<<TXT
            Para participar en la subasta, sigue estos pasos:

            1️⃣ Compra tus bases a S/ 50.00
            🛒 Disponibles del 09 al 20 de junio de 2025

            Presencial: Pago en el Banco de Crédito (Cuenta Corriente N° 193-11271150-99 o CCI:00219300112711509914 a nombre de EMILIMA S.A.) y presentación del comprobante en la Subgerencia de Tesorería.
            Virtual: A través de la página web www.emilima.com.pe/home.

            📩 Enviar el voucher al correo subasta@emilima.com.pe.
            Una vez validado, recibirás las bases en PDF y el comprobante de pago correspondiente.


            2️⃣ Depósito de garantía
            Deberás entregar un cheque de gerencia no negociable, según el tipo de espacio:

            Para espacios en el Parque de la Exposición:
            Monto: equivalente a 2 meses de renta mensual (ver Anexo 01)
            A nombre de: Municipalidad Metropolitana de Lima (RUC 20131380951)

            Para inmuebles del Cercado de Lima:
            Monto: equivalente a 3 meses de renta mensual
            A nombre de: EMILIMA S.A. (RUC 20126236078)

            📍 Entrega presencial del cheque en:
            Jr. Cuzco N° 286, Cercado de Lima – Subgerencia de Tesorería y Recaudación
            🕐 Horario: 8:30 a.m. a 1:00 p.m. y 2:00 p.m. a 5:00 p.m.
            📅 Hasta el viernes 20 de junio de 2025

            📌 Tras revisión del cheque, se te entregará el recibo de caja, único documento que te acredita como postor hábil.
            TXT,
            '6' => <<<TXT
            📋 Requisitos para participar:

            📌 Para personas naturales:

            Anexo 03 – Declaración Jurada (Descargar PDF)
            Declaración Jurada de procedencia lícita de fondos (Descargar PDF)
            Copia de DNI
            Comprobante de compra de bases emitido por EMILIMA S.A.
            Recibo de caja por concepto de garantía emitido por EMILIMA S.A.

            📌 Para personas jurídicas:

            Anexo 03 – Declaración Jurada (Descargar PDF)
            Declaración Jurada de procedencia lícita de fondos (Descargar PDF)
            Copia de DNI del representante legal
            Copia de RUC y vigencia de poder (SUNARP – no mayor a 30 días)
            Comprobante de compra de bases emitido por EMILIMA S.A.
            Recibo de caja por concepto de garantía emitido por EMILIMA S.A.

            📆 Fecha del acto de subasta:
            Lunes 23 de junio de 2025
            📍 Lugar: Museo Metropolitano de Lima – Sala Taulichusco (Av. 28 de julio con Av. Garcilaso de la Vega – Parque de la Exposición, Cercado de Lima)
            ⏰ Hora: 3:00 p.m. (máxima tolerancia: 10 minutos)
            🔹 Modalidad: Presencial
            TXT,
            '7' => <<<TXT
            📍 Oficina: Jr. Cuzco N° 286, Cercado de Lima
            📲 Celulares: 989-346-982 / 987-658-263
            🌐 Web: www.emilima.com.pe/home

            📞 Nuestro equipo está listo para responder todas tus consultas en nuestros canales oficiales.
            TXT,
        ];

        // Detectar "hola"
        if (Str::contains($comentario, ['hola','Hola','buenos','dias','subasta','informacion','información'])) {
            $respuesta = <<<MENU
            👋 ¡Hola! Soy Emi, el asistente virtual de la Empresa Municipal Inmobiliaria de Lima - EMILIMA.

            Hemos lanzado la convocatoria para nuestras subastas públicas y estoy aquí para brindarte toda la información que necesites. 📢

            SUBASTA DE TERRENOS:
            1️⃣ Ver la lista de terrenos en subasta 📜🏡
            2️⃣ ¿Cómo participar en la subasta de terrenos? 🏢📈
            3️⃣ Fechas y requisitos para participar en la subasta de terrenos 📅✅

            SUBASTA DE ARRENDAMIENTO DE ESPACIOS COMERCIALES:
            4️⃣ Ver los espacios comerciales disponibles para arrendamiento 🛍️📌
            5️⃣ ¿Cómo participar en la subasta de arrendamiento? 💼📊
            6️⃣ Fechas y requisitos para participar en la subasta de arrendamiento 🗓️📋

            OTROS:
            7️⃣ Contacto 📞📩

            🔹 Escribe el número de la opción que deseas.
            🔹 Escribe "menú" para ver nuevamente las opciones.
            🔹 Escribe "salir" para cerrar el chat.
            MENU;
        }// Detectar opciones 1 al 4
        elseif (array_key_exists($comentario, $opciones)) {
            $respuesta = $opciones[$comentario];
        }// Detectar menú
        elseif (Str::contains($comentario, ['menu', 'menú'])) {
            $respuesta = <<<MENU
            SUBASTA DE TERRENOS:
            1️⃣ Ver la lista de terrenos en subasta 📜🏡
            2️⃣ ¿Cómo participar en la subasta de terrenos? 🏢📈
            3️⃣ Fechas y requisitos para participar en la subasta de terrenos 📅✅

            SUBASTA DE ARRENDAMIENTO DE ESPACIOS COMERCIALES:
            4️⃣ Ver los espacios comerciales disponibles para arrendamiento 🛍️📌
            5️⃣ ¿Cómo participar en la subasta de arrendamiento? 💼📊
            6️⃣ Fechas y requisitos para participar en la subasta de arrendamiento 🗓️📋

            OTROS:
            7️⃣ Contacto 📞📩

            🔹 Escribe el número de la opción que deseas.
            🔹 Escribe "menú" para ver nuevamente las opciones.
            🔹 Escribe "salir" para cerrar el chat.
            MENU;
        }// Detectar salida
        elseif (Str::contains($comentario, ['salir','ADIOS','adios','Adios','Adiós', 'hasta luego','Hasta luego'])) {
            $respuesta = <<<SALIDA
            Gracias por contactarte con EMILIMA. 👋
            Si necesitas más información, no dudes en volver a escribirnos.
            ¡Que tengas un excelente día! ☀️
            SALIDA;
        }
        else {
            $respuesta = <<<NO_OPCION
            Gracias por comunicarte con la Empresa Inmobiliaria de Lima - EMILIMA 🔝🏙️ Soy Emi y espero haber resuelto tus consultas 👍🏼
            Si necesitas algo más, no dudes en contactarme 👋🏼😉 ¡Que tengas un excelente día!
            NO_OPCION;
        }
/** Lo siento 😥, no entendí tu mensaje.   Por favor, escribe "hola" o un número del 1 al 4 o escribe "menú" para ver las opciones disponibles.            Escribe "salir" para cerrar el chat. */
        // Enviar mensaje
        $response = Http::withOptions($this->ws_responder_texto($numero, $respuesta))
            ->post($endpoint);

        if ($response->failed()) {
            Log::info("❌ Error al enviar el mensaje");
            Log::info($response->body());
        } else {
            Log::info("✅ Mensaje enviado correctamente");
            Log::info($response->body());
        }
    }

  function extraerValoresEnviarRespuesta($req)
  {
    try {
      $entry = $req['entry'][0];
      $changes = $entry['changes'][0];
      $value = $changes['value'];
      $from = $value['metadata']['display_phone_number'];

      if (isset($value['messages'][0]['interactive']['button_reply']['id'])) {
        $id_boton = $value['messages'][0]['interactive']['button_reply']['id'];

        if($id_boton=='registro'){
          Log::info('Se presiono el boton Registro');
        };

        if($id_boton=='ver_balance'){
          Log::info('Se presiono el boton Ver balance');
        };

        if($id_boton=='realizar_pago'){
          Log::info('Se presiono el boton Realizar pago');
        };

      }



      $objetomensaje = $value['messages'];
      $mensaje = $objetomensaje[0];

      $comentario = $mensaje['text']['body'];
      $numero = $mensaje['from'];
      $id = $mensaje['id'];
      $timestamp = $mensaje['timestamp'];

      // Buscar o crear usuario
      $chatbotusuario = ChatbotUsuario::updateOrCreate(
            ['numero_telefono' => $numero],
            ['ultima_interaccion' => Carbon::createFromTimestamp($timestamp)]
        );

        // Guardar mensaje
        ChatbotMensaje::create([
            'chatbot_usuario_id' => $chatbotusuario->id,
            'mensaje_id' => $id  ?? null,
            'tipo_mensaje' => 'text',
            'contenido' => $comentario ,
            'fecha_envio' => Carbon::createFromTimestamp($timestamp),
            'creado_por_chatbot' => false,
        ]);

        Log::info('Mensaje guardado exitosamente.');

      $this->enviarRespuesta($comentario, $numero, $id, $timestamp, $from);

      $response = Response::make(json_encode(['message' => 'EVENT_RECEIVED']), 200);
      $response->header('Content-Type', 'application/json');
    } catch (Exception $e) {

      $response = Response::make(json_encode(['message' => 'EVENT_RECEIVED']), 200);
      $response->header('Content-Type', 'application/json');
    }
  }


  function escuchar(Request $request)
  {

    if ($request->isMethod('post')) {

      $data = $request->json()->all(); // Obtiene el JSON como array

      // Log::info($data);

      $this->extraerValoresEnviarRespuesta($data);

    } else {
        Log::info('No llegó nada');
    }

  }


  function token()
  {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

      if (isset($_GET['hub_mode']) && isset($_GET['hub_verify_token']) && isset($_GET['hub_challenge']) && $_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === $this->ws_tokenwebhook) {

        Log::info($_GET['hub_mode']);
        Log::info($_GET['hub_verify_token']);
        Log::info($_GET['hub_challenge']);

        echo $_GET['hub_challenge'];
      } else {
        http_response_code(403);
        Log::info('Fallo...');
      }
    }
  }


  public function verificarEnv()
    {
        return response()->json([
            'ws_token' => $this->ws_token,
            'ws_endpoint' => $this->ws_endpoint,
            'ws_tokenwebhook' => $this->ws_tokenwebhook,
        ]);
    }

    // Nuevo método para verificar interacciones inactivas
    public function checkInactiveInteractions()
    {
        $inactiveInteractions = WhatsappInteraction::where('last_interaction', '<', now()->subMinutes(5))
            ->where('auto_message_sent', false)
            ->get();

        foreach ($inactiveInteractions as $interaction) {
            $this->sendAutoMessage($interaction->phone_number);
            $interaction->update(['auto_message_sent' => true]);
        }
    }

    // Nuevo método para enviar mensaje automático
    private function sendAutoMessage($numero)
    {
        $respuesta = <<<CINCOMINSININTERACCION
        Gracias por comunicarte con la Empresa Inmobiliaria de Lima - EMILIMA 🔝🏙️ Soy Emi y espero haber resuelto tus consultas 👍🏼
        Si necesitas algo más, no dudes en contactarme 👋🏼😉 ¡Que tengas un excelente día!
        CINCOMINSININTERACCION;

        $response = Http::withOptions($this->ws_responder_texto($numero, $respuesta))
            ->post($this->ws_endpoint);

        if ($response->failed()) {
            Log::error("Error al enviar mensaje automático a $numero: " . $response->body());
        } else {
            Log::info("Mensaje automático enviado correctamente a $numero");
        }
    }

}
