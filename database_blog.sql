-- ============================================================
-- BLOG POSTS TABLE
-- ============================================================
-- Tabela para armazenar os posts do blog
-- Execute este SQL no phpMyAdmin da Hostinger
-- ============================================================

CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    resumo TEXT,
    conteudo LONGTEXT NOT NULL,
    imagem_destaque VARCHAR(255) DEFAULT NULL,
    meta_description VARCHAR(160),
    ativo TINYINT(1) DEFAULT 1,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_ativo (ativo),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ARTIGOS INICIAIS (SEO)
-- ============================================================

INSERT INTO blog_posts (titulo, slug, resumo, conteudo, meta_description, ativo) VALUES 

-- Artigo 1: Dog Walker em Osasco
('Dog Walker em Osasco: O Guia Completo para Passeios com seu Cão',
'dog-walker-osasco-guia-completo',
'Descubra tudo sobre o serviço de Dog Walker em Osasco: benefícios, preços, como escolher e por que seu cão precisa de passeios regulares.',
'<h2>O que é um Dog Walker?</h2>
<p>Dog Walker é o profissional especializado em passear com cães. Mais do que simplesmente "andar com o cachorro", um bom Dog Walker entende de <strong>comportamento canino</strong>, sabe como lidar com diferentes raças e temperamentos, e garante que seu pet tenha uma experiência segura e enriquecedora.</p>

<h2>Por que contratar um Dog Walker em Osasco?</h2>
<p>Osasco é uma cidade movimentada, e muitos tutores trabalham fora o dia todo. Sem passeios regulares, cães podem desenvolver:</p>
<ul>
<li>Ansiedade e estresse</li>
<li>Comportamentos destrutivos</li>
<li>Obesidade e problemas de saúde</li>
<li>Excesso de energia à noite</li>
</ul>

<h2>Benefícios dos Passeios com GPS</h2>
<p>Na <strong>Curicas Pet</strong>, todos os passeios são monitorados com <strong>GPS em tempo real</strong>. Você acompanha cada passo do seu pet pelo celular e recebe fotos e vídeos pelo WhatsApp.</p>

<h3>Áreas que Atendemos</h3>
<p>Nosso serviço de Dog Walker atende Osasco e região, incluindo bairros como <strong>Pinheiros, Lapa, Boaçava, Alto de Pinheiros, Butantã e Vila Leopoldina</strong>.</p>

<h2>Quanto Custa um Dog Walker?</h2>
<p>O valor médio de um passeio em Osasco varia de R$40 a R$80, dependendo da duração e frequência. Na Curicas Pet, oferecemos pacotes especiais para passeios diários.</p>

<h2>Solicite seu Orçamento</h2>
<p>Entre em contato conosco e solicite um orçamento gratuito. Seu pet merece os melhores passeios!</p>',
'Dog Walker em Osasco com GPS em tempo real. Passeios seguros para seu cão em Osasco, Pinheiros, Lapa e região. Orçamento grátis!',
1),

-- Artigo 2: Hotel Pet vs Pet Sitter
('Hotel Pet vs Pet Sitter: Qual a Melhor Opção para seu Cão?',
'hotel-pet-vs-pet-sitter',
'Compare Hotel Pet e Pet Sitter para descobrir qual serviço é ideal para seu cão durante viagens ou ausências prolongadas.',
'<h2>Vai Viajar e Não Sabe Onde Deixar seu Cão?</h2>
<p>Essa é uma das maiores preocupações dos tutores de pets. Duas opções se destacam: <strong>Hotel Pet</strong> e <strong>Pet Sitter</strong>. Mas qual escolher?</p>

<h2>O que é Hotel Pet?</h2>
<p>O Hotel Pet é um estabelecimento preparado para receber cães durante a ausência dos tutores. Oferece:</p>
<ul>
<li>Ambiente controlado e seguro</li>
<li>Monitoramento 24 horas</li>
<li>Socialização com outros cães</li>
<li>Passeios inclusos</li>
<li>Alimentação regular</li>
</ul>

<h2>O que é Pet Sitter?</h2>
<p>O Pet Sitter é um cuidador que vai até a <strong>sua casa</strong> para cuidar do pet. Ideal para:</p>
<ul>
<li>Cães que não se adaptam bem fora de casa</li>
<li>Pets idosos ou com necessidades especiais</li>
<li>Quando você tem outros animais em casa</li>
</ul>

<h2>Comparativo: Hotel Pet x Pet Sitter</h2>
<table>
<tr><th>Critério</th><th>Hotel Pet</th><th>Pet Sitter</th></tr>
<tr><td>Local</td><td>Estabelecimento</td><td>Sua casa</td></tr>
<tr><td>Socialização</td><td>Alta</td><td>Baixa</td></tr>
<tr><td>Conforto</td><td>Bom</td><td>Excelente</td></tr>
<tr><td>Custo</td><td>Moderado</td><td>Variável</td></tr>
</table>

<h2>Nossa Recomendação</h2>
<p>Na <strong>Curicas Pet</strong>, oferecemos ambos os serviços! Nosso Hotel Pet é <strong>sem gaiolas</strong>, com ambiente humanizado. E nosso Pet Sitter cuida do seu pet com todo carinho na sua própria casa.</p>',
'Hotel Pet ou Pet Sitter? Compare os dois serviços e descubra qual é ideal para seu cão. Curicas Pet oferece ambos em Osasco!',
1),

-- Artigo 3: Preparar Cão para Primeiro Passeio
('Como Preparar seu Cão para o Primeiro Passeio com Dog Walker',
'preparar-cao-primeiro-passeio',
'Dicas essenciais para preparar seu cão para o primeiro passeio com um Dog Walker profissional. Adaptação gradual e segura.',
'<h2>Seu Cão Vai Começar a Passear com um Dog Walker?</h2>
<p>A primeira experiência é fundamental! Um bom início garante que seu pet associe os passeios a momentos positivos. Confira nossas dicas:</p>

<h2>1. Apresentação Prévia</h2>
<p>Antes do primeiro passeio, peça para o Dog Walker fazer uma visita prévia. Isso permite que:</p>
<ul>
<li>O cão conheça o profissional em ambiente familiar</li>
<li>Você explique as particularidades do seu pet</li>
<li>O Dog Walker avalie o comportamento do cão</li>
</ul>

<h2>2. Equipamentos Adequados</h2>
<p>Tenha sempre disponível:</p>
<ul>
<li>Coleira e guia em bom estado</li>
<li>Peitoral (recomendado para cães que puxam)</li>
<li>Saquinhos para coleta</li>
<li>Água fresca</li>
</ul>

<h2>3. Informações Importantes</h2>
<p>Informe ao Dog Walker:</p>
<ul>
<li>Medos ou traumas do cão</li>
<li>Reatividade com outros cães ou pessoas</li>
<li>Problemas de saúde</li>
<li>Comandos que o cão conhece</li>
</ul>

<h2>4. Comece Gradualmente</h2>
<p>Na primeira semana, passeios mais curtos ajudam na adaptação. Depois, aumente o tempo gradualmente.</p>

<h2>Na Curicas Pet</h2>
<p>Nossos Dog Walkers são <strong>certificados em comportamento canino</strong> e fazem a adaptação gradual com cada novo pet. Solicite uma visita gratuita!</p>',
'Dicas para preparar seu cão para o primeiro passeio com Dog Walker. Adaptação segura e gradual. Curicas Pet em Osasco.',
1),

-- Artigo 4: Escolher Dog Walker de Confiança
('5 Dicas para Escolher um Dog Walker de Confiança',
'dicas-escolher-dog-walker-confianca',
'Saiba como escolher um Dog Walker profissional e confiável. Critérios essenciais para garantir a segurança do seu pet.',
'<h2>Como Saber se um Dog Walker é Confiável?</h2>
<p>Entregar seu pet nas mãos de um estranho exige confiança. Veja os critérios que separamos para você fazer a escolha certa:</p>

<h2>1. Verifique Referências e Avaliações</h2>
<p>Procure por:</p>
<ul>
<li>Avaliações no Google</li>
<li>Depoimentos de outros clientes</li>
<li>Tempo de atuação no mercado</li>
</ul>

<h2>2. Pergunte Sobre Certificações</h2>
<p>Um bom Dog Walker deve ter:</p>
<ul>
<li>Curso de comportamento canino</li>
<li>Conhecimento em primeiros socorros pet</li>
<li>Experiência comprovada</li>
</ul>

<h2>3. Exija Monitoramento por GPS</h2>
<p>Serviços profissionais oferecem <strong>GPS em tempo real</strong>. Isso garante que você saiba exatamente onde seu pet está durante o passeio.</p>

<h2>4. Peça Fotos e Relatórios</h2>
<p>Dog Walkers sérios enviam:</p>
<ul>
<li>Fotos durante o passeio</li>
<li>Vídeos pelo WhatsApp</li>
<li>Relatório de comportamento</li>
</ul>

<h2>5. Faça uma Visita de Avaliação</h2>
<p>Antes de contratar, peça uma visita gratuita. Observe como o profissional interage com seu cão.</p>

<h2>Por que Escolher a Curicas Pet?</h2>
<p>A Curicas Pet oferece todos esses diferenciais: <strong>GPS em tempo real, fotos diárias, equipe certificada e mais de 500 pets atendidos</strong>. Solicite seu orçamento!</p>',
'5 dicas para escolher um Dog Walker confiável. GPS, certificações e avaliações. Curicas Pet - Dog Walker em Osasco.',
1),

-- Artigo 5: Passeios com GPS
('Passeios com GPS: Por Que é Importante Acompanhar seu Pet em Tempo Real',
'passeios-gps-tempo-real',
'Descubra a importância do GPS em tempo real nos passeios de cães. Segurança, transparência e tranquilidade para os tutores.',
'<h2>O Que São Passeios com GPS?</h2>
<p>Passeios com GPS são aqueles onde o Dog Walker utiliza um aplicativo de rastreamento, permitindo que você acompanhe <strong>em tempo real</strong> o trajeto do seu pet.</p>

<h2>Benefícios do GPS em Tempo Real</h2>

<h3>1. Segurança</h3>
<p>Você sabe exatamente onde seu pet está a qualquer momento. Em caso de emergência, a localização é imediata.</p>

<h3>2. Transparência</h3>
<p>O GPS comprova que o passeio foi realizado conforme combinado: tempo, distância e rota.</p>

<h3>3. Tranquilidade</h3>
<p>Mesmo no trabalho, você pode abrir o celular e ver seu pet passeando. Isso reduz a ansiedade de deixar o cão com terceiros.</p>

<h3>4. Histórico de Passeios</h3>
<p>Você tem acesso ao histórico de todos os passeios, podendo acompanhar a evolução do seu pet.</p>

<h2>Como Funciona na Curicas Pet?</h2>
<ol>
<li>O Dog Walker inicia o passeio no app</li>
<li>Você recebe um link no WhatsApp</li>
<li>Acompanha o trajeto em tempo real</li>
<li>Recebe fotos e vídeos durante o passeio</li>
<li>Ao final, recebe o resumo da caminhada</li>
</ol>

<h2>Experimente!</h2>
<p>Venha conhecer nosso serviço de Dog Walker com GPS em Osasco, Pinheiros, Lapa, Boaçava e região. Orçamento gratuito em 2 minutos!</p>',
'Passeios com GPS em tempo real para cães. Segurança e transparência. Curicas Pet - Dog Walker em Osasco e Zona Oeste SP.',
1);
