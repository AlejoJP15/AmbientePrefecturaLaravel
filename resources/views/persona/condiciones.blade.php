@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('styles/login.css') }}">
<link rel="stylesheet" href="{{ asset('styles/registro_user.css') }}">

<style>
    .condiciones-wrapper {
        background: rgba(255, 255, 255, 0.95);
        max-width: 900px;
        margin: 40px auto;
        padding: 30px;
        border-radius: 10px;
        overflow-y: auto;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .condiciones-wrapper h4,
    .condiciones-wrapper h5 {
        text-align: center;
        color: #37008c;
        margin-bottom: 20px;
    }

    .condiciones-wrapper img {
        display: block;
        margin: 20px auto;
        max-width: 200px;
        height: auto;
    }

    .condiciones-wrapper p {
        text-align: justify;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    @media (max-width: 600px) {
        .condiciones-wrapper {
            margin: 20px;
            padding: 20px;
        }

        .condiciones-wrapper img {
            max-width: 150px;
        }
    }
</style>

<div class="condiciones-wrapper">
    <h4>Honorable Gobierno Autónomo Descentralizado de la Provincia de Chimborazo</h4>

    <img src="{{ asset('img/logo_login.jpg') }}" alt="Logo">

    <h5>Condiciones de uso</h5>
                <div>
                                &nbsp;
                            </div>
                            <p>Las presentes condiciones generales de uso de la página web, regulan los términos y condiciones de acceso y uso de www.chimborazo.gob.ec, propiedad de GAD de la Provincia de Chimborazo, con domicilio en Riobamba-Ecuador, en adelante, «la Empresa», que el usuario del Portal deberá de leer y aceptar para usar todos los servicios e información que se facilitan desde el portal. El mero acceso y/o utilización del portal, de todos o parte de sus contenidos y/o servicios significa la plena aceptación de las presentes condiciones generales de uso.</p>

                            <p>
                                Condiciones de uso
                                Las presentes condiciones generales de uso del portal regulan el acceso y la utilización del portal, incluyendo los contenidos y los servicios puestos a disposición de los usuarios en y/o a través del portal, bien por el portal, bien por sus usuarios, bien por terceros. No obstante, el acceso y la utilización de ciertos contenidos y/o servicios pueden encontrarse sometido a determinadas condiciones específicas.
                            </p>
                            <p>
                                Modificaciones
                                La empresa se reserva la facultad de modificar en cualquier momento las condiciones generales de uso del portal. En todo caso, se recomienda que consulte periódicamente los presentes términos de uso del portal, ya que pueden ser modificados.
                            </p>
                            <p>
                                Obligaciones del Usuario
                                El usuario deberá respetar en todo momento los términos y condiciones establecidos en las presentes condiciones generales de uso del portal. De forma expresa el usuario manifiesta que utilizará el portal de forma diligente y asumiendo cualquier responsabilidad que pudiera derivarse del incumplimiento de las normas.
                            </p>
                            <p>
                                Así mismo, el usuario no podrá utilizar el portal para transmitir, almacenar, divulgar promover o distribuir datos o contenidos que sean portadores de virus o cualquier otro código informático, archivos o programas diseñados para interrumpir, destruir o perjudicar el funcionamiento de cualquier programa o equipo informático o de telecomunicaciones.
                            </p>
                            <p>
                                Responsabilidad del portal
                                El usuario conoce y acepta que el portal no otorga ninguna garantía de cualquier naturaleza, ya sea expresa o implícita, sobre los datos, contenidos, información y servicios que se incorporan y ofrecen desde el Portal.
                            </p>
                            <p>
                                Exceptuando los casos que la Ley imponga expresamente lo contrario, y exclusivamente con la medida y extensión en que lo imponga, el Portal no garantiza ni asume responsabilidad alguna respecto a los posibles daños y perjuicios causados por el uso y utilización de la información, datos y servicios del Portal.
                            </p>
                            <p>
                                En todo caso, el Portal excluye cualquier responsabilidad por los daños y perjuicios que puedan deberse a la información y/o servicios prestados o suministrados por terceros diferentes de la Empresa. Toda responsabilidad será del tercero ya sea proveedor o colaborador.
                            </p>
                            <p>
                                Propiedad intelectual e industrial
                                Todos los contenidos, marcas, logos, dibujos, documentación, programas informáticos o cualquier otro elemento susceptible de protección por la legislación de propiedad intelectual o industrial, que sean accesibles en el portal corresponden exclusivamente a la empresa o a sus legítimos titulares y quedan expresamente reservados todos los derechos sobre los mismos. Queda expresamente prohibida la creación de enlaces de hipertexto (links) a cualquier elemento integrante de las páginas web del Portal sin la autorización de la empresa, siempre que no sean a una página web del Portal que no requiera identificación o autenticación para su acceso, o el mismo esté restringido.
                            </p>
                            <p>
                                En cualquier caso, el portal se reserva todos los derechos sobre los contenidos, información datos y servicios que ostente sobre los mismos. El portal no concede ninguna licencia o autorización de uso al usuario sobre sus contenidos, datos o servicios, distinta de la que expresamente se detalle en las presentes condiciones generales de uso del portal.
                            </p>
                            <p>
                                Legislación aplicable, jurisdicción competente y notificaciones
                                Las presentes condiciones se rigen y se interpretan de acuerdo con las Leyes de Ecuador. Para cualquier reclamación serán competentes los juzgados y tribunales de Riobamba. Todas las notificaciones, requerimientos, peticiones y otras comunicaciones que el Usuario desee efectuar a la Empresa titular del Portal deberán realizarse por escrito y se entenderá que han sido correctamente realizadas cuando hayan sido recibidas en la siguiente dirección (prefectura@chimborazo.gob.ec).
                            </p>
                            <p>
                                Las presentes condiciones generales de uso de la página web, regulan los términos y condiciones de acceso y uso de www.chimborazo.gob.ec, propiedad de GAD de la Provincia de Chimborazo, con domicilio en Riobamba-Ecuador, en adelante, «la Empresa», que el usuario del Portal deberá de leer y aceptar para usar todos los servicios e información que se facilitan desde el portal. El mero acceso y/o utilización del portal, de todos o parte de sus contenidos y/o servicios significa la plena aceptación de las presentes condiciones generales de uso.
                            </p>
                            <p>
                                Condiciones de uso
                                Las presentes condiciones generales de uso del portal regulan el acceso y la utilización del portal, incluyendo los contenidos y los servicios puestos a disposición de los usuarios en y/o a través del portal, bien por el portal, bien por sus usuarios, bien por terceros. No obstante, el acceso y la utilización de ciertos contenidos y/o servicios pueden encontrarse sometido a determinadas condiciones específicas.
                            </p>
                            <p>
                                Modificaciones
                                La empresa se reserva la facultad de modificar en cualquier momento las condiciones generales de uso del portal. En todo caso, se recomienda que consulte periódicamente los presentes términos de uso del portal, ya que pueden ser modificados.
                            </p>
                            <p>
                                Obligaciones del Usuario
                                El usuario deberá respetar en todo momento los términos y condiciones establecidos en las presentes condiciones generales de uso del portal. De forma expresa el usuario manifiesta que utilizará el portal de forma diligente y asumiendo cualquier responsabilidad que pudiera derivarse del incumplimiento de las normas.
                            </p>
                            <p>
                                Así mismo, el usuario no podrá utilizar el portal para transmitir, almacenar, divulgar promover o distribuir datos o contenidos que sean portadores de virus o cualquier otro código informático, archivos o programas diseñados para interrumpir, destruir o perjudicar el funcionamiento de cualquier programa o equipo informático o de telecomunicaciones.
                            </p>
                            <p>
                                Responsabilidad del portal
                                El usuario conoce y acepta que el portal no otorga ninguna garantía de cualquier naturaleza, ya sea expresa o implícita, sobre los datos, contenidos, información y servicios que se incorporan y ofrecen desde el Portal.
                            </p>
                            <p>
                                Exceptuando los casos que la Ley imponga expresamente lo contrario, y exclusivamente con la medida y extensión en que lo imponga, el Portal no garantiza ni asume responsabilidad alguna respecto a los posibles daños y perjuicios causados por el uso y utilización de la información, datos y servicios del Portal.
                            </p>
                            <p>
                                En todo caso, el Portal excluye cualquier responsabilidad por los daños y perjuicios que puedan deberse a la información y/o servicios prestados o suministrados por terceros diferentes de la Empresa. Toda responsabilidad será del tercero ya sea proveedor o colaborador.
                            </p>
                            <p>
                                Propiedad intelectual e industrial
                                Todos los contenidos, marcas, logos, dibujos, documentación, programas informáticos o cualquier otro elemento susceptible de protección por la legislación de propiedad intelectual o industrial, que sean accesibles en el portal corresponden exclusivamente a la empresa o a sus legítimos titulares y quedan expresamente reservados todos los derechos sobre los mismos. Queda expresamente prohibida la creación de enlaces de hipertexto (links) a cualquier elemento integrante de las páginas web del Portal sin la autorización de la empresa, siempre que no sean a una página web del Portal que no requiera identificación o autenticación para su acceso, o el mismo esté restringido.
                            </p>
                            <p>
                                En cualquier caso, el portal se reserva todos los derechos sobre los contenidos, información datos y servicios que ostente sobre los mismos. El portal no concede ninguna licencia o autorización de uso al usuario sobre sus contenidos, datos o servicios, distinta de la que expresamente se detalle en las presentes condiciones generales de uso del portal.
                            </p>
                            <p>
                                Legislación aplicable, jurisdicción competente y notificaciones
                                Las presentes condiciones se rigen y se interpretan de acuerdo con las Leyes de Ecuador. Para cualquier reclamación serán competentes los juzgados y tribunales de Riobamba. Todas las notificaciones, requerimientos, peticiones y otras comunicaciones que el Usuario desee efectuar a la Empresa titular del Portal deberán realizarse por escrito y se entenderá que han sido correctamente realizadas cuando hayan sido recibidas en la siguiente dirección (prefectura@chimborazo.gob.ec).
                            </p>
                        </div>
            
        </div>
    </div>
</div>
@endsection
