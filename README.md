# 🐾 Curicas Pet — Plataforma Web & Gestão de Serviços Pet

Aplicação web completa multipágina com arquitetura **MVC (Model-View-Controller)** desenvolvida em **PHP nativo**, **MySQL** e scripts auxiliares em **Python**, voltada para agendamento de serviços, orçamentos dinâmicos, blog de conteúdo e painel administrativo integrado.

---

## 🛠️ Stack Tecnológica & Arquitetura

* **Linguagem & Backend:** PHP 8.x estruturado sob o padrão arquitetural MVC
* **Banco de Dados:** MySQL (estruturas relacionais para cotações, blog, serviços e galeria)
* **Frontend:** Bootstrap 5, HTML5 Semântico, CSS3 customizado e JavaScript modular
* **Automação & Utilitários:** Python (geração programática e customizada de QR Codes de alta resolução com Pillow/qrcode)
* **Servidor & SEO:** Apache (`.htaccess` com URL Rewriting, gzip e headers de segurança), geração dinâmica de `sitemap.xml` e controle de `robots.txt`

---

## 💼 Módulos e Recursos da Aplicação

* **Painel Administrativo & Gestão de Negócio:**
  * Autenticação e controle de sessão administrativa.
  * Dashboard de acompanhamento com detalhamento de cotações e orçamentos recebidos.
  * Gestão de serviços, galeria de imagens e controle de publicações do Blog.
  * Módulo dedicado para cadastro e intermediação de adoção de pets.

* **Front-Office & Experiência do Usuário (Multipágina):**
  * Motor de cálculo e solicitação de orçamentos online.
  * Integração direta com fluxos de atendimento via WhatsApp API.
  * Otimização para mecanismos de busca (SEO Técnico, Open Graph e layouts responsivos).
  * Redirecionamentos estratégicos e páginas dinâmicas de captação via QR Code.

---

## 🚀 Como Executar o Projeto Localmente

### Pré-requisitos
* PHP 8.0+
* MySQL / MariaDB
* Servidor Apache ou PHP Built-in Server

### Instalação
\`\`\`bash
# Clonar o repositório
git clone git@github.com:PauloBrisoti/curicas-pet.git

# Importar o banco de dados
mysql -u seu_usuario -p seu_banco < database.sql

# Iniciar o servidor embutido do PHP (para testes locais)
php -S localhost:8000
\`\`\`
