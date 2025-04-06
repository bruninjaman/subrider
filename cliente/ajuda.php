<?php
require_once '../includes/session_manager.php';

$session = new SessionManager();

// Verifica se o cliente está logado
if (!$session->isClienteLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Ajuda - Área do Cliente';
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title mb-4">Central de Ajuda</h2>
                    
                    <!-- Perguntas Frequentes -->
                    <section class="mb-5">
                        <h3 class="h4 mb-4">Perguntas Frequentes</h3>
                        
                        <div class="accordion" id="faq">
                            <!-- Acesso -->
                            <div class="accordion-item">
                                <h4 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#faq-acesso">
                                        Como faço para acessar minha conta?
                                    </button>
                                </h4>
                                <div id="faq-acesso" class="accordion-collapse collapse show">
                                    <div class="accordion-body">
                                        <p>Para acessar sua conta, você precisa:</p>
                                        <ol>
                                            <li>Ter um cadastro ativo na oficina</li>
                                            <li>Solicitar suas credenciais de acesso na oficina</li>
                                            <li>Acessar a página de login com seu email e senha</li>
                                        </ol>
                                        <p>Se você esqueceu sua senha, use a opção "Esqueceu sua senha?" 
                                           na página de login.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Motos -->
                            <div class="accordion-item">
                                <h4 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#faq-motos">
                                        Como visualizo minhas motos?
                                    </button>
                                </h4>
                                <div id="faq-motos" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <p>Suas motos são exibidas no dashboard e na seção "Minhas Motos". 
                                           Para cada moto, você pode:</p>
                                        <ul>
                                            <li>Ver detalhes completos</li>
                                            <li>Acessar o histórico de serviços</li>
                                            <li>Verificar o histórico de proprietários</li>
                                        </ul>
                                        <p>Se alguma moto não estiver aparecendo, entre em contato com a oficina.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ordens de Serviço -->
                            <div class="accordion-item">
                                <h4 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#faq-os">
                                        Como acompanho minhas ordens de serviço?
                                    </button>
                                </h4>
                                <div id="faq-os" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <p>Você pode acompanhar suas ordens de serviço de várias formas:</p>
                                        <ul>
                                            <li>No dashboard, onde aparecem as ordens mais recentes</li>
                                            <li>Na seção "Histórico de Serviços"</li>
                                            <li>Na página individual de cada moto</li>
                                        </ul>
                                        <p>As ordens de serviço mostram:</p>
                                        <ul>
                                            <li>Status atual do serviço</li>
                                            <li>Descrição do serviço</li>
                                            <li>Data de entrada e previsão</li>
                                            <li>Valores e forma de pagamento</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Notificações -->
                            <div class="accordion-item">
                                <h4 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#faq-notificacoes">
                                        Como configuro minhas notificações?
                                    </button>
                                </h4>
                                <div id="faq-notificacoes" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <p>Você pode personalizar suas notificações nas Preferências:</p>
                                        <ul>
                                            <li>Ative/desative notificações por email</li>
                                            <li>Ative/desative notificações por WhatsApp</li>
                                            <li>Escolha quais tipos de atualização deseja receber</li>
                                        </ul>
                                        <p>Você receberá notificações sobre:</p>
                                        <ul>
                                            <li>Atualizações de status das ordens de serviço</li>
                                            <li>Conclusão de serviços</li>
                                            <li>Lembretes de manutenção</li>
                                            <li>Informações importantes da oficina</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Contato -->
                    <section class="mb-5">
                        <h3 class="h4 mb-4">Precisa de Mais Ajuda?</h3>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-phone-alt text-primary"></i> Telefone
                                        </h5>
                                        <p class="card-text">
                                            Entre em contato pelo telefone:<br>
                                            <strong>(XX) XXXX-XXXX</strong>
                                        </p>
                                        <p class="card-text text-muted">
                                            Segunda a Sexta: 08h às 18h<br>
                                            Sábado: 08h às 12h
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-envelope text-primary"></i> Email
                                        </h5>
                                        <p class="card-text">
                                            Envie um email para:<br>
                                            <strong>contato@subrider.com.br</strong>
                                        </p>
                                        <p class="card-text text-muted">
                                            Respondemos em até 24 horas úteis
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Termos e Políticas -->
                    <section>
                        <h3 class="h4 mb-4">Documentos Úteis</h3>
                        
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-alt"></i> Termos de Uso
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-shield-alt"></i> Política de Privacidade
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-book"></i> Manual do Usuário
                            </a>
                        </div>
                    </section>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar para o Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 