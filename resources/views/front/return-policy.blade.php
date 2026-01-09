@extends('front.layout')
@section('page-title', 'Política de Devolução - Terras no Paraguay | Termos e Condições')
@section('meta-description', 'Conheça nossa política de devolução e cancelamento para transações imobiliárias.')
@section('pageHeading')
    @if(!empty($currentLang))
        @if($currentLang->code == 'pt')
            Política de Devolução
        @elseif($currentLang->code == 'en')
            Return Policy
        @else
            Política de Devolución
        @endif
    @else
        Política de Devolução
    @endif
@endsection

@section('content')
<!-- DEBUG: Lang Code = {{ $currentLang->code ?? 'NULL' }} -->
<section class="return-policy-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @if(!empty($currentLang) && $currentLang->code == 'pt')
                            <h2>Política de Devolução</h2>
                            
                            <h4>1. Prazo de Devolução</h4>
                            <p>Não aceitamos devoluções para propriedades imobiliárias. Todas as vendas são finais após a assinatura do contrato.</p>
                            
                            <h4>2. Cancelamento antes da Conclusão</h4>
                            <p>Cancelamentos podem ser solicitados antes da assinatura final do contrato, sujeitos às condições contratuais e possíveis multas conforme acordado.</p>
                            
                            <h4>3. Garantias</h4>
                            <p>As propriedades são vendidas conforme descrito nos anúncios. Todas as informações são verificadas, mas recomendamos inspeção prévia.</p>
                            
                            <h4>4. Contato</h4>
                            <p>Para dúvidas sobre transações, entre em contato:</p>
                            <ul>
                                <li>Email: {{ $bs->support_email ?? 'contato@terrasnoparaguay.com' }}</li>
                                <li>Telefone: {{ $bs->support_phone ?? '+595 994 718400' }}</li>
                            </ul>
                        
                        @elseif(!empty($currentLang) && $currentLang->code == 'en')
                            <h2>Return Policy</h2>
                            
                            <h4>1. Return Period</h4>
                            <p>We do not accept returns for real estate properties. All sales are final after contract signature.</p>
                            
                            <h4>2. Cancellation before Completion</h4>
                            <p>Cancellations may be requested before final contract signature, subject to contractual conditions and possible penalties as agreed.</p>
                            
                            <h4>3. Warranties</h4>
                            <p>Properties are sold as described in listings. All information is verified, but we recommend prior inspection.</p>
                            
                            <h4>4. Contact</h4>
                            <p>For transaction inquiries, please contact:</p>
                            <ul>
                                <li>Email: {{ $bs->support_email ?? 'contact@terrasnoparaguay.com' }}</li>
                                <li>Phone: {{ $bs->support_phone ?? '+595 994 718400' }}</li>
                            </ul>
                        
                        @else
                            <h2>Política de Devolución</h2>
                            
                            <h4>1. Período de Devolución</h4>
                            <p>No aceptamos devoluciones para propiedades inmobiliarias. Todas las ventas son finales después de la firma del contrato.</p>
                            
                            <h4>2. Cancelación antes de la Conclusión</h4>
                            <p>Las cancelaciones pueden solicitarse antes de la firma final del contrato, sujetas a condiciones contractuales y posibles multas según lo acordado.</p>
                            
                            <h4>3. Garantías</h4>
                            <p>Las propiedades se venden según se describe en los anuncios. Toda la información se verifica, pero recomendamos inspección previa.</p>
                            
                            <h4>4. Contacto</h4>
                            <p>Para consultas sobre transacciones, contacte:</p>
                            <ul>
                                <li>Email: {{ $bs->support_email ?? 'contacto@terrasnoparaguay.com' }}</li>
                                <li>Teléfono: {{ $bs->support_phone ?? '+595 994 718400' }}</li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
