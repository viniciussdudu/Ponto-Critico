# 🍴 Ponto-Crítico: Sistema de Review e Catálogo

Este projeto foi desenvolvido como parte da disciplina de **Engenharia de Software** do curso de **Ciência da Computação** do 1° Semestre de 2026 com o **Professor Edeilson Milhomem**. A aplicação utiliza a arquitetura **MVC (Model-View-Controller)** e persistência de dados em arquivos **JSON**, seguindo rigorosamente o fluxo de trabalho **GitFlow**.

O **Ponto-Crítico** é uma plataforma inspirada no *Letterboxd*, permitindo que usuários avaliem e compartilhem críticas sobre filmes, livros e jogos em um ambiente centralizado.

## 👥 Equipe e Atribuições

| Integrante | Função e Responsabilidades |
| :--- | :--- |
| **Vinicius Eduardo** | Arquiteto de Software, responsável pelo setup inicial do projeto e pela implementação do sistema de autenticação e cadastro de usuários. |
| **Gustavo Bringel** | Desenvolvedor, responsável pela implementação do sistema de catálogo e cadastro de mídias (filmes, livros e jogos). |
| **Arthur Bispo** | Desenvolvedor, responsável pela implementação do sistema de registro de avaliações e notas dos usuários. |
| **Klaus Henrique** | Desenvolvedor, responsável pela implementação do motor de visualização e exibição das avaliações e dados do catálogo. |
| **Samara Coelho** | Desenvolvedora, responsável pela implementação do fluxo de recuperação de acesso e redefinição de senha. |

## 🛠️ Tecnologias Utilizadas

* **Linguagem:** PHP 8.x
* **Arquitetura:** MVC (Model-View-Controller)
* **Banco de Dados:** Persistência via arquivos JSON
* **Versionamento:** Git (Branching Model: GitFlow)

## 🚀 Como Rodar o Projeto Localmente

1.  Certifique-se de ter o **PHP 8.x** instalado.
2.  Clone o repositório:
    ```bash
    git clone [https://github.com/viniciussdudu/Ponto-Critico.git](https://github.com/viniciussdudu/Ponto-Critico.git)
    ```
3.  Inicie o servidor embutido do PHP apontando para a pasta pública:
    ```bash
    php -S localhost:8000 -t Public
    ```
4.  Abra o navegador e acesse: `http://localhost:8000`

## 🔄 Fluxo de Desenvolvimento (GitFlow)

O projeto seguiu o seguinte padrão de branches:
* **main:** Versão estável (Release).
* **develop:** Branch de integração.
* **feature/:** Desenvolvimento individual de funcionalidades (ex: `feature/cadastro-midias`, `feature/recuperar-senha`).
