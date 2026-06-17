# Engenharia de Software - 2026.1 | Universidade Federal do Tocantins - Palmas

### Curso: Bacharelado em Ciência da Computação

### Professor: Edeilson Milhomem da Silva

### Time: Vinicius Eduardo De Sousa Silveira, Arthur Pereira Bispo, Klaus Henrique Otaviano Souza, Gustavo Leite Brigel, Samara Coelho da Silva

# Ponto-Crítico: Sistema de Review e Catálogo
## Visão geral:

O Ponto Crítico é uma plataforma web desenvolvida para a centralização e compartilhamento de críticas e avaliações de mídias (filmes, séries, jogos e livros). O projeto nasceu da necessidade de criar um ambiente colaborativo onde entusiastas podem catalogar suas experiências culturais e interagir com as opiniões de outros usuários.

<img src="Public/img/logo2.pontocritico.png" alt="Ponto Crítico" width="1000px"/>


## Funcionalidades Principais:

Gestão de Usuários: Cadastro, login e edição de perfil personalizado.

Catálogo de Mídias: Sistema para registro e listagem de diferentes tipos de conteúdo.

Sistema de Avaliações: Funcionalidade core que permite atribuir notas e críticas textuais vinculadas ao perfil do usuário.

## 🔗 Links Úteis

| Recurso | Link |
|--------|------|
| 🎥 Vídeo Demonstrativo | [vídeo](https://drive.google.com/file/d/1jz5t6644rvW_oxLb5JXZQP00z8dMuzuN/view?usp=sharing) |
| 🌐 Sistema Online (Landing Page) | [Landing Page](https://viniciussdudu.github.io/Landing-Page-Ponto-Cr-tico/) |
| 🏷️ Versão final | [Release](https://github.com/viniciussdudu/Ponto-Critico/releases/tag/V5.0) |
| 📝 Apresentação Final | [Apresentação](docs/Apresentação%20final/Apresentação_Final_Ponto_Crítico.pdf) |
<hr>

## Sprints

[sprint 1](docs/Sprints/Sprint%201_%20Cadastro%20e%20avalia%C3%A7%C3%A3o%20das%20m%C3%ADdias%20pdf.pdf) : [Release](https://github.com/viniciussdudu/Ponto-Critico/releases/tag/v1.0)

[sprint 2](docs/Sprints/Sprint%202_%20Edi%C3%A7%C3%A3o%20do%20perfil%20do%20usu%C3%A1rio%20e%20de%20avalia%C3%A7%C3%B5es%20pdf.pdf) : [Release](https://github.com/viniciussdudu/Ponto-Critico/releases/tag/V2.0)

[sprint 3](docs/Sprints/Sprint%203_%20Edi%C3%A7%C3%A3o%20do%20perfil%20do%20usu%C3%A1rio%20e%20de%20avalia%C3%A7%C3%B5es.pdf) : [Release](https://github.com/viniciussdudu/Ponto-Critico/releases/tag/V3.0)

[sprint 4](docs/Sprints/Sprint%204_%20%20APIs,%20Mídias,%20Interação%20e%20Auditoria%20.pdf) : [Release](https://github.com/viniciussdudu/Ponto-Critico/releases/tag/V4.0)

[sprint 5](docs/Sprints/Sprint%205_%20Listas,%20pesquisa%20e%20visualização%20de%20mídias%20.pdf) : [Release](https://github.com/viniciussdudu/Ponto-Critico/releases/tag/V5.0)

## Planejamento do Projeto 

[GitHub Projects](https://github.com/users/viniciussdudu/projects/1)


# Como Executar o Projeto Localmente

1. **Pré-requisitos:**
   * Certifique-se de ter o **XAMPP** instalado (com suporte ao PHP 8.x).
   * Certifique-se de ter o **PostgreSQL** e o **pgAdmin** instalados e rodando.

2. **Clone o repositório dentro do htdocs do XAMPP:**
   Navegue até a pasta `htdocs` do seu XAMPP (geralmente em `C:\xampp\htdocs\`) e clone o projeto:
   ```bash
   git clone https://github.com/viniciussdudu/Ponto-Critico.git
3. Certifique-se de ter o Composer instalado.
       No Terminal digite:
     ```bash
    composer install
    ```

4. Configuração do Banco de Dados:
   * Abra o pgAdmin e crie um novo banco de dados com o nome ponto_critico

   * clone o arquivo Ponto_critico.SQL

   * modifique o arquivo App/Models/Database.php com as suas configurações do PostgreSQL

5. Abra o terminal do XAMPPP e clique em Start no Module Apache

6. Configurar Envio de Email de Confirmações:
   * Criar conta no Mailtrap.io
   * modifique o arquivo App/Services/EmailService.php com as suas configurações do Mailtrap.io




   
