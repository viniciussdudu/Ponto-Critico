--
-- PostgreSQL database dump
--

-- Dumped from database version 17.4
-- Dumped by pg_dump version 17.4

-- Started on 2026-06-14 20:28:27

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 859 (class 1247 OID 24590)
-- Name: tipo_voto; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.tipo_voto AS ENUM (
    'like',
    'dislike'
);


ALTER TYPE public.tipo_voto OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 224 (class 1259 OID 24643)
-- Name: avaliacao; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.avaliacao (
    id_avaliacao integer NOT NULL,
    comentario text,
    nota integer NOT NULL,
    data_avaliacao timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    id_midia character varying(50) NOT NULL,
    id_usuario integer NOT NULL,
    CONSTRAINT avaliacao_nota_check CHECK (((nota >= 0) AND (nota <= 5)))
);


ALTER TABLE public.avaliacao OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 24642)
-- Name: avaliacao_id_avaliacao_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.avaliacao_id_avaliacao_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.avaliacao_id_avaliacao_seq OWNER TO postgres;

--
-- TOC entry 4990 (class 0 OID 0)
-- Dependencies: 223
-- Name: avaliacao_id_avaliacao_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.avaliacao_id_avaliacao_seq OWNED BY public.avaliacao.id_avaliacao;


--
-- TOC entry 226 (class 1259 OID 24664)
-- Name: comentario; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.comentario (
    id_comentario integer NOT NULL,
    conteudo text NOT NULL,
    data_criacao timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    id_avaliacao integer NOT NULL,
    id_usuario integer NOT NULL
);


ALTER TABLE public.comentario OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 24663)
-- Name: comentario_id_comentario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.comentario_id_comentario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.comentario_id_comentario_seq OWNER TO postgres;

--
-- TOC entry 4991 (class 0 OID 0)
-- Dependencies: 225
-- Name: comentario_id_comentario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.comentario_id_comentario_seq OWNED BY public.comentario.id_comentario;


--
-- TOC entry 221 (class 1259 OID 24615)
-- Name: listas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.listas (
    id_lista integer NOT NULL,
    nome_lista character varying(100) NOT NULL,
    data_criacao timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    id_usuario integer NOT NULL
);


ALTER TABLE public.listas OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 24614)
-- Name: listas_id_lista_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.listas_id_lista_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.listas_id_lista_seq OWNER TO postgres;

--
-- TOC entry 4992 (class 0 OID 0)
-- Dependencies: 220
-- Name: listas_id_lista_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.listas_id_lista_seq OWNED BY public.listas.id_lista;


--
-- TOC entry 229 (class 1259 OID 24714)
-- Name: logs_acesso; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.logs_acesso (
    id_log integer NOT NULL,
    email character varying(150),
    data timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    ip character varying(45),
    evento character varying(255)
);


ALTER TABLE public.logs_acesso OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 24713)
-- Name: logs_acesso_id_log_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.logs_acesso_id_log_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.logs_acesso_id_log_seq OWNER TO postgres;

--
-- TOC entry 4993 (class 0 OID 0)
-- Dependencies: 228
-- Name: logs_acesso_id_log_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.logs_acesso_id_log_seq OWNED BY public.logs_acesso.id_log;


--
-- TOC entry 219 (class 1259 OID 24607)
-- Name: midia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.midia (
    id_midia character varying(50) NOT NULL,
    titulo character varying(255) NOT NULL,
    data_lancamento date,
    tipo_midia character varying(50) NOT NULL,
    genero character varying(50) NOT NULL,
    sinopse text,
    capa_midia character varying(255)
);


ALTER TABLE public.midia OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 24627)
-- Name: midias_lista; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.midias_lista (
    posicao integer,
    id_midia character varying(50) NOT NULL,
    id_lista integer NOT NULL
);


ALTER TABLE public.midias_lista OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 24596)
-- Name: usuario; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuario (
    id_usuario integer NOT NULL,
    tipo_usuario character varying(50) DEFAULT 'user'::character varying,
    token character varying(255),
    nome character varying(100) NOT NULL,
    email character varying(150) NOT NULL,
    bio text,
    foto_perfil character varying(255),
    senha character varying(255),
    status integer DEFAULT 0
);


ALTER TABLE public.usuario OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 24595)
-- Name: usuario_id_usuario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuario_id_usuario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuario_id_usuario_seq OWNER TO postgres;

--
-- TOC entry 4994 (class 0 OID 0)
-- Dependencies: 217
-- Name: usuario_id_usuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuario_id_usuario_seq OWNED BY public.usuario.id_usuario;


--
-- TOC entry 230 (class 1259 OID 24746)
-- Name: votos_avaliacao; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.votos_avaliacao (
    id_usuario integer NOT NULL,
    id_avaliacao integer NOT NULL,
    curtida character varying(10)
);


ALTER TABLE public.votos_avaliacao OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 24683)
-- Name: votos_midia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.votos_midia (
    id_usuario integer NOT NULL,
    id_midia character varying(50) NOT NULL,
    curtida public.tipo_voto
);


ALTER TABLE public.votos_midia OWNER TO postgres;

--
-- TOC entry 4786 (class 2604 OID 24646)
-- Name: avaliacao id_avaliacao; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.avaliacao ALTER COLUMN id_avaliacao SET DEFAULT nextval('public.avaliacao_id_avaliacao_seq'::regclass);


--
-- TOC entry 4788 (class 2604 OID 24667)
-- Name: comentario id_comentario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comentario ALTER COLUMN id_comentario SET DEFAULT nextval('public.comentario_id_comentario_seq'::regclass);


--
-- TOC entry 4784 (class 2604 OID 24618)
-- Name: listas id_lista; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.listas ALTER COLUMN id_lista SET DEFAULT nextval('public.listas_id_lista_seq'::regclass);


--
-- TOC entry 4790 (class 2604 OID 24717)
-- Name: logs_acesso id_log; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_acesso ALTER COLUMN id_log SET DEFAULT nextval('public.logs_acesso_id_log_seq'::regclass);


--
-- TOC entry 4781 (class 2604 OID 24599)
-- Name: usuario id_usuario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario ALTER COLUMN id_usuario SET DEFAULT nextval('public.usuario_id_usuario_seq'::regclass);


--
-- TOC entry 4978 (class 0 OID 24643)
-- Dependencies: 224
-- Data for Name: avaliacao; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.avaliacao (id_avaliacao, comentario, nota, data_avaliacao, id_midia, id_usuario) FROM stdin;
42	bom	5	2026-06-14 17:49:39.798389	mid_6a298aed24b57	1
43	Pior que o cara realmente morreu no começo do filme	4	2026-06-14 20:11:42.103755	mid_6a2f34a15a0c5	1
44	Bom e Dificil kskskksks	5	2026-06-14 20:12:02.436127	mid_6a2f34fa4fdbd	1
45	Here's Johnny!!!!\r\n\r\n\r\nMuito bom o filme	5	2026-06-14 20:24:16.469506	mid_6a2f35e423b51	1
\.


--
-- TOC entry 4980 (class 0 OID 24664)
-- Dependencies: 226
-- Data for Name: comentario; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.comentario (id_comentario, conteudo, data_criacao, id_avaliacao, id_usuario) FROM stdin;
\.


--
-- TOC entry 4975 (class 0 OID 24615)
-- Dependencies: 221
-- Data for Name: listas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.listas (id_lista, nome_lista, data_criacao, id_usuario) FROM stdin;
9	favoritos	2026-06-10 13:23:37.596291	1
11	meus favoritos	2026-06-14 17:56:50.708294	4
\.


--
-- TOC entry 4983 (class 0 OID 24714)
-- Dependencies: 229
-- Data for Name: logs_acesso; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.logs_acesso (id_log, email, data, ip, evento) FROM stdin;
\.


--
-- TOC entry 4973 (class 0 OID 24607)
-- Dependencies: 219
-- Data for Name: midia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.midia (id_midia, titulo, data_lancamento, tipo_midia, genero, sinopse, capa_midia) FROM stdin;
mid_6a298aed24b57	Pecadores	2025-04-18	Filme	Terror	Dois irmãos gêmeos tentam deixar suas vidas problemáticas para trás e retornam à cidade natal para recomeçar. Lá, eles descobrem que um mal ainda maior está à espreita para recebê-los de volta.	c604e04e2b4c7ed74cd8c4bf8ec19c04.webp
mid_6a2f34a15a0c5	Laranja Mecânica	1972-04-26	Filme	Drama	O jovem Alex passa as noites com uma gangue de amigos briguentos. Depois que é preso, se submete a uma técnica de modificação de comportamento para poder ganhar sua liberdade.	29fd2f26d3bf5a23fe6ad0227c9bf318.jpg
mid_6a2f34fa4fdbd	Elden Ring	2022-02-25	Jogo	Ação	Elden Ring é um jogo eletrônico Souls Like de mundo aberto em terceira pessoa, desenvolvido pela FromSoftware e publicado pela Bandai Namco Entertainment. O jogo é um projeto colaborativo entre o diretor Hidetaka Miyazaki e o romancista de fantasia George R. R. Martin.	869fa659dfb93c8ee3190df5d19af5fd.jpg
mid_6a2f35e423b51	O Iluminado	1980-12-25	Filme	Terror	Jack Torrance se torna caseiro de inverno do isolado Hotel Overlook, nas montanhas do Colorado, na esperança de curar seu bloqueio de escritor. Ele se instala com a esposa Wendy e o filho Danny, que é atormentado por premonições. Jack não consegue escrever e as visões de Danny se tornam mais perturbadoras. O escritor descobre os segredos sombrios do hotel e começa a se transformar em um maníaco homicida, aterrorizando sua família.	b3d142a49a26f08f966350c7fc6c1910.jpg
\.


--
-- TOC entry 4976 (class 0 OID 24627)
-- Dependencies: 222
-- Data for Name: midias_lista; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.midias_lista (posicao, id_midia, id_lista) FROM stdin;
1	mid_6a298aed24b57	11
1	mid_6a298aed24b57	9
2	mid_6a2f34a15a0c5	9
3	mid_6a2f34fa4fdbd	9
4	mid_6a2f35e423b51	9
\.


--
-- TOC entry 4972 (class 0 OID 24596)
-- Dependencies: 218
-- Data for Name: usuario; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuario (id_usuario, tipo_usuario, token, nome, email, bio, foto_perfil, senha, status) FROM stdin;
2	user	2f6a61d0ac0d23bc2d709b8ed91686bb	neymar	neymar@gmail.com	\N	\N	$2y$10$JLyEMmvL.MnIw.q/UeyvAeSXRZ134mFXzuFu2bs6INh7FWtmfjoiO	0
3	admin	\N	Testador	teste@teste.com	\N	\N	senha_criptografada_aqui	0
4	user	\N	mario	mario@gmail.com	\N	\N	$2y$10$.F4rpvbSFpnZ78zI.UL6Relc0Re2zAtRd6JOMWM/HwvZwQpMO9jQK	1
1	admin	36dc3b16422c8e6f319c8bec24b2a61a	vinicius	viniteste2@gmail.com	Um amante do cinema bom	8a72a202d8967bc18045b2c2edaa1063.png	$2y$10$OrLlmVBdXpmMthe2sIQ/qeAi9.t5rE97M2nBXJFqBeNSus2UAR2vi	1
\.


--
-- TOC entry 4984 (class 0 OID 24746)
-- Dependencies: 230
-- Data for Name: votos_avaliacao; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.votos_avaliacao (id_usuario, id_avaliacao, curtida) FROM stdin;
1	42	like
1	45	like
\.


--
-- TOC entry 4981 (class 0 OID 24683)
-- Dependencies: 227
-- Data for Name: votos_midia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.votos_midia (id_usuario, id_midia, curtida) FROM stdin;
\.


--
-- TOC entry 4995 (class 0 OID 0)
-- Dependencies: 223
-- Name: avaliacao_id_avaliacao_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.avaliacao_id_avaliacao_seq', 45, true);


--
-- TOC entry 4996 (class 0 OID 0)
-- Dependencies: 225
-- Name: comentario_id_comentario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.comentario_id_comentario_seq', 4, true);


--
-- TOC entry 4997 (class 0 OID 0)
-- Dependencies: 220
-- Name: listas_id_lista_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.listas_id_lista_seq', 11, true);


--
-- TOC entry 4998 (class 0 OID 0)
-- Dependencies: 228
-- Name: logs_acesso_id_log_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.logs_acesso_id_log_seq', 1, false);


--
-- TOC entry 4999 (class 0 OID 0)
-- Dependencies: 217
-- Name: usuario_id_usuario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuario_id_usuario_seq', 4, true);


--
-- TOC entry 4804 (class 2606 OID 24652)
-- Name: avaliacao avaliacao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.avaliacao
    ADD CONSTRAINT avaliacao_pkey PRIMARY KEY (id_avaliacao);


--
-- TOC entry 4808 (class 2606 OID 24672)
-- Name: comentario comentario_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comentario
    ADD CONSTRAINT comentario_pkey PRIMARY KEY (id_comentario);


--
-- TOC entry 4800 (class 2606 OID 24621)
-- Name: listas listas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.listas
    ADD CONSTRAINT listas_pkey PRIMARY KEY (id_lista);


--
-- TOC entry 4812 (class 2606 OID 24720)
-- Name: logs_acesso logs_acesso_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.logs_acesso
    ADD CONSTRAINT logs_acesso_pkey PRIMARY KEY (id_log);


--
-- TOC entry 4798 (class 2606 OID 24613)
-- Name: midia midia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.midia
    ADD CONSTRAINT midia_pkey PRIMARY KEY (id_midia);


--
-- TOC entry 4802 (class 2606 OID 24631)
-- Name: midias_lista midias_lista_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.midias_lista
    ADD CONSTRAINT midias_lista_pkey PRIMARY KEY (id_midia, id_lista);


--
-- TOC entry 4806 (class 2606 OID 24725)
-- Name: avaliacao unica_avaliacao_por_usuario; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.avaliacao
    ADD CONSTRAINT unica_avaliacao_por_usuario UNIQUE (id_usuario, id_midia);


--
-- TOC entry 4794 (class 2606 OID 24606)
-- Name: usuario usuario_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_email_key UNIQUE (email);


--
-- TOC entry 4796 (class 2606 OID 24604)
-- Name: usuario usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (id_usuario);


--
-- TOC entry 4814 (class 2606 OID 24750)
-- Name: votos_avaliacao votos_avaliacao_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.votos_avaliacao
    ADD CONSTRAINT votos_avaliacao_pkey PRIMARY KEY (id_usuario, id_avaliacao);


--
-- TOC entry 4810 (class 2606 OID 24687)
-- Name: votos_midia votos_midia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.votos_midia
    ADD CONSTRAINT votos_midia_pkey PRIMARY KEY (id_usuario, id_midia);


--
-- TOC entry 4818 (class 2606 OID 24653)
-- Name: avaliacao avaliacao_id_midia_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.avaliacao
    ADD CONSTRAINT avaliacao_id_midia_fkey FOREIGN KEY (id_midia) REFERENCES public.midia(id_midia) ON DELETE CASCADE;


--
-- TOC entry 4819 (class 2606 OID 24658)
-- Name: avaliacao avaliacao_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.avaliacao
    ADD CONSTRAINT avaliacao_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario) ON DELETE CASCADE;


--
-- TOC entry 4820 (class 2606 OID 24673)
-- Name: comentario comentario_id_avaliacao_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comentario
    ADD CONSTRAINT comentario_id_avaliacao_fkey FOREIGN KEY (id_avaliacao) REFERENCES public.avaliacao(id_avaliacao) ON DELETE CASCADE;


--
-- TOC entry 4821 (class 2606 OID 24678)
-- Name: comentario comentario_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comentario
    ADD CONSTRAINT comentario_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario) ON DELETE CASCADE;


--
-- TOC entry 4824 (class 2606 OID 24756)
-- Name: votos_avaliacao fk_votos_avaliacao; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.votos_avaliacao
    ADD CONSTRAINT fk_votos_avaliacao FOREIGN KEY (id_avaliacao) REFERENCES public.avaliacao(id_avaliacao) ON DELETE CASCADE;


--
-- TOC entry 4825 (class 2606 OID 24751)
-- Name: votos_avaliacao fk_votos_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.votos_avaliacao
    ADD CONSTRAINT fk_votos_usuario FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario) ON DELETE CASCADE;


--
-- TOC entry 4815 (class 2606 OID 24622)
-- Name: listas listas_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.listas
    ADD CONSTRAINT listas_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario) ON DELETE CASCADE;


--
-- TOC entry 4816 (class 2606 OID 24637)
-- Name: midias_lista midias_lista_id_lista_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.midias_lista
    ADD CONSTRAINT midias_lista_id_lista_fkey FOREIGN KEY (id_lista) REFERENCES public.listas(id_lista) ON DELETE CASCADE;


--
-- TOC entry 4817 (class 2606 OID 24632)
-- Name: midias_lista midias_lista_id_midia_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.midias_lista
    ADD CONSTRAINT midias_lista_id_midia_fkey FOREIGN KEY (id_midia) REFERENCES public.midia(id_midia) ON DELETE CASCADE;


--
-- TOC entry 4822 (class 2606 OID 24693)
-- Name: votos_midia votos_midia_id_midia_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.votos_midia
    ADD CONSTRAINT votos_midia_id_midia_fkey FOREIGN KEY (id_midia) REFERENCES public.midia(id_midia) ON DELETE CASCADE;


--
-- TOC entry 4823 (class 2606 OID 24688)
-- Name: votos_midia votos_midia_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.votos_midia
    ADD CONSTRAINT votos_midia_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario) ON DELETE CASCADE;


-- Completed on 2026-06-14 20:28:27

--
-- PostgreSQL database dump complete
--

