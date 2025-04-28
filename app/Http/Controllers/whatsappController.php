<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;
use App\Models\ChatbotUsuario;
use App\Models\ChatbotMensaje;

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

        $opciones = [
            '1' => <<<TXT
            📌 Tenemos 8 terrenos disponibles en Lima Metropolitana.
            Cada uno cuenta con distintas características y precios base. Puedes revisar el listado completo y detalles en este enlace: 🔗 [https://beacons.ai/emilima.sa]
            TXT,

            '2' => <<<TXT
            Para participar en la subasta, sigue estos pasos:

            1️⃣ Compra tus bases a S/50.00

            Presencial: Pago en el Banco de Crédito (Cuenta Corriente N° 193-11271150-99 a nombre de EMILIMA S.A.) y presentación del comprobante en la Subgerencia de Tesorería.
            Virtual: A través de la página web www.emilima.com.pe.

            2️⃣ Inscripción y depósito de garantía

            Presencial: Jr. Cuzco N° 286, Cercado de Lima (mesa de partes).
            Virtual: www.sgd.emilima.com.pe/mesapartesvirtual.html.

            📌 Inscripciones hasta el viernes 23 de mayo. Para más detalles, revisa las bases en: [https://beacons.ai/emilima.sa]
            TXT,

            '3' => <<<TXT
            📆 Fecha de la subasta: Domingo 25 de mayo 2025
            📍 Lugar: Museo Metropolitano de Lima (Sala Taulichusco) Av. 28 de julio con Av. Garcilaso de la Vega -Parque de la Exposición, Cercado de Lima
            ⏰ Hora: 9:00 a.m.

            🔹 Modalidad: Mixta (presencial y virtual para postores fuera de Lima Metropolitana)

            📋 Requisitos para participar:

            📌 Para personas naturales:
            🔹 Copia del DNI.
            🔹 Declaración jurada de no tener deudas con la Municipalidad de Lima.
            🔹 Comprobante de pago de bases.
            🔹 Comprobante de depósito de garantía.

            📌 Para personas jurídicas:
            🔹 Copia del RUC y vigencia de poder del representante legal.
            🔹 Copia del DNI del representante legal.
            🔹 Declaración jurada de no tener deudas con la Municipalidad de Lima.
            🔹 Comprobante de pago de bases.
            🔹 Comprobante de depósito de garantía.
            TXT,

            '4' => <<<TXT
            📍 Oficina: Jr. Cuzco N° 286, Cercado de Lima
            📲 Celulares: 989-346-982 / 987-658-263
            🌐 Web: www.emilima.com.pe

            Nuestro equipo está listo para responder todas tus dudas.
            TXT,
        ];

        // Detectar "hola"
        if (Str::contains($comentario, 'hola')) {
            $respuesta = <<<MENU
            ____________________________________________________________________
            👋 ¡Hola! Soy Emi, el asistente virtual de la Empresa Municipal Inmobiliaria de Lima - EMILIMA.
            ____________________________________________________________________
            Hemos lanzado la convocatoria y estoy aquí para brindarte toda la información que necesites. 📢

            1 Ver la lista de inmuebles en subasta 📜🏡
            2 Cómo participar en la subasta 🏢📈
            3 Fechas y requisitos para participar 📅✅
            4 Contacto 📞📩

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
            1 Ver la lista de inmuebles en subasta 📜🏡
            2 Cómo participar en la subasta 🏢📈
            3 Fechas y requisitos para participar 📅✅
            4 Contacto 📞📩

            🔹 Escribe el número de la opción que deseas.
            🔹 Escribe "menú" para ver nuevamente las opciones.
            🔹 Escribe "salir" para cerrar el chat.
            MENU;
        }// Detectar salida
        elseif (Str::contains($comentario, 'salir')) {
            $respuesta = <<<SALIDA
            Gracias por contactarte con EMILIMA. 👋
            Si necesitas más información, no dudes en volver a escribirnos.
            ¡Que tengas un excelente día! ☀️
            SALIDA;
        }// Opción no válida
        else {
            $respuesta = <<<NO_OPCION
            Lo siento 😥, no entendí tu mensaje.
            Por favor, escribe un número del 1 al 4 o escribe "menú" para ver las opciones disponibles.
            Escribe "salir" para cerrar el chat.
            NO_OPCION;
        }

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
      $tipo_mensaje = $value['messages'][0]['type'];




      $objetomensaje = $value['messages'];
      $mensaje = $objetomensaje[0];

      $comentario = $mensaje['text']['body'];
      $numero = $mensaje['from'];
      $id = $mensaje['id'];
      $timestamp = $mensaje['timestamp'];


      $contenido = '';
      if ($tipo_mensaje == 'text') {
          $contenido = $value['messages'][0]['text']['body'];
      } elseif ($tipo_mensaje == 'interactive') {
          $contenido = $value['messages'][0]['interactive']['button_reply']['id'];
      } else {
          $contenido = json_encode($value['messages'][0]);
      }
      // Buscar o crear usuario
      $chatbotusuario = ChatbotUsuario::updateOrCreate(
            ['numero_telefono' => $from],
            ['ultima_interaccion' => Carbon::createFromTimestamp($timestamp)]
        );

        // Guardar mensaje
        ChatbotMensaje::create([
            'chatbot_usuario_id' => $chatbotusuario->id,
            'mensaje_id' => $id  ?? null,
            'tipo_mensaje' => $tipo_mensaje,
            'contenido' => $contenido,
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


}
