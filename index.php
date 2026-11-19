<?php
/**
 * PROJETO ACADÊMICO: SUSTENTA+ — Tecnologia, Informação e Sustentabilidade
 * EQUIPE 03: Alimentação Sustentável — Do campo ao prato: produzir, consumir e desperdiçar
 * ARQUIVO: index.php (Receitas com Moderação Prévia do Admin e Paginação "Ver Mais")
 */

require_once 'conexao.php';

$alerta_js = '';

// ===================================================================
// PROCESSAMENTO DOS FORMULÁRIOS PÚBLICOS VIA POST
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {

    // 1. Envio de Nova Receita Sustentável (Salva como 'pendente' para aprovação do ADM)
    if ($_POST['acao'] === 'nova_receita') {
        $titulo           = trim($_POST['titulo'] ?? '');
        $categoria        = trim($_POST['categoria'] ?? 'Aproveitamento Integral');
        $ingredientes     = trim($_POST['ingredientes'] ?? '');
        $modo_preparo     = trim($_POST['modo_preparo'] ?? '');
        $dica_sustentavel = trim($_POST['dica_sustentavel'] ?? '');
        $autor            = trim($_POST['autor'] ?? 'Visitante Colaborador');
        $imagem_url       = trim($_POST['imagem_url'] ?? '');

        if ($imagem_url === '') {
            $imagem_url = 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&fit=crop&w=800&q=80';
        }

        $categorias_permitidas = ['Aproveitamento Integral', 'Vegana/Vegetariana', 'Rápida & Econômica', 'Tradicional Regional'];
        if (!in_array($categoria, $categorias_permitidas, true)) {
            $categoria = 'Aproveitamento Integral';
        }

        if ($titulo !== '' && $ingredientes !== '' && $modo_preparo !== '') {
            $stmt = $pdo->prepare("INSERT INTO `receitas` 
                (`titulo`, `categoria`, `ingredientes`, `modo_preparo`, `dica_sustentavel`, `autor`, `imagem_url`, `status`) 
                VALUES (:t, :c, :i, :m, :d, :a, :img, 'pendente')");
            $stmt->execute([
                ':t'   => $titulo,
                ':c'   => $categoria,
                ':i'   => $ingredientes,
                ':m'   => $modo_preparo,
                ':d'   => $dica_sustentavel !== '' ? $dica_sustentavel : 'Aproveite integralmente cada ingrediente e destine aparas cruas à compostagem.',
                ':a'   => $autor,
                ':img' => $imagem_url
            ]);
            $_SESSION['msg_flash'] = "Receita enviada com sucesso! Ela ficará visível no site assim que for revisada e aprovada pelo Administrador.";
            header("Location: index.php#ferramenta-receitas");
            exit;
        }
    }

    // 2. Envio de Comentário Moderado (Salva como 'pendente' para aprovação do ADM)
    if ($_POST['acao'] === 'novo_comentario') {
        $tipo_origem  = ($_POST['tipo_origem'] ?? '') === 'local' ? 'local' : 'receita';
        $item_id      = (int)($_POST['item_id'] ?? 0);
        $nome_usuario = trim($_POST['nome_usuario'] ?? '');
        $nota         = (int)($_POST['nota'] ?? 5);
        $texto        = trim($_POST['texto'] ?? '');

        if ($nota < 1) $nota = 1;
        if ($nota > 5) $nota = 5;

        if ($item_id > 0 && $nome_usuario !== '' && $texto !== '') {
            $stmt = $pdo->prepare("INSERT INTO `comentarios` 
                (`tipo_origem`, `item_id`, `nome_usuario`, `nota`, `texto`, `status`) 
                VALUES (:tipo, :id, :nome, :nota, :texto, 'pendente')");
            $stmt->execute([
                ':tipo'  => $tipo_origem,
                ':id'    => $item_id,
                ':nome'  => $nome_usuario,
                ':nota'  => $nota,
                ':texto' => $texto
            ]);
            $_SESSION['msg_flash'] = "Comentário enviado com sucesso! Ele ficará visível assim que for aprovado pelo Administrador.";
            $ancora = ($tipo_origem === 'local') ? '#ferramenta-mapa' : '#ferramenta-receitas';
            header("Location: index.php" . $ancora);
            exit;
        }
    }
}

if (isset($_SESSION['msg_flash'])) {
    $alerta_js = $_SESSION['msg_flash'];
    unset($_SESSION['msg_flash']);
}

// ===================================================================
// CONSULTAS AO BANCO DE DADOS PARA EXIBIÇÃO DINÂMICA
// ===================================================================
$receitas_db = $pdo->query("SELECT * FROM `receitas` WHERE `status` = 'aprovado' ORDER BY `id` ASC")->fetchAll();
$locais_db   = $pdo->query("SELECT * FROM `locais_sustentaveis` ORDER BY `id` ASC")->fetchAll();
$plantio_db  = $pdo->query("SELECT * FROM `guia_plantio` ORDER BY `id` ASC")->fetchAll();
$comentarios_aprovados = $pdo->query("SELECT * FROM `comentarios` WHERE `status` = 'aprovado' ORDER BY `data_comentario` DESC")->fetchAll();

$com_por_receita = [];
$com_por_local   = [];

foreach ($comentarios_aprovados as $c) {
    if ($c['tipo_origem'] === 'receita') {
        $com_por_receita[$c['item_id']][] = $c;
    } else {
        $com_por_local[$c['item_id']][] = $c;
    }
}

foreach ($receitas_db as &$r) {
    $r['comentarios'] = $com_por_receita[$r['id']] ?? [];
}
unset($r);

foreach ($locais_db as &$l) {
    $l['comentarios'] = $com_por_local[$l['id']] ?? [];
}
unset($l);

$pendentes_com_nav = (int)$pdo->query("SELECT COUNT(*) FROM `comentarios` WHERE `status` = 'pendente'")->fetchColumn();
$pendentes_rec_nav = (int)$pdo->query("SELECT COUNT(*) FROM `receitas` WHERE `status` = 'pendente'")->fetchColumn();
$total_pendentes_nav = $pendentes_com_nav + $pendentes_rec_nav;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUSTENTA+ | Equipe 03 — Alimentação Sustentável: Do campo ao prato</title>
    <meta name="description" content="Projeto SUSTENTA+ Equipe 03: Alimentação Sustentável — Do campo ao prato: produzir, consumir e desperdiçar.">

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        laranja: {
                            50: '#FFF7ED',
                            100: '#FFEDD5',
                            500: '#F97316',
                            600: '#EA580C',
                            700: '#C2410C',
                        },
                        oliva: {
                            50: '#F7FEE7',
                            100: '#ECFCCB',
                            600: '#4D7C0F',
                            700: '#3F6212',
                            800: '#365314',
                            900: '#1A2E05',
                        },
                        creme: '#FAF8F5',
                        grafite: '#1C1917'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Folha de Estilo Customizada -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="antialiased selection:bg-laranja-500 selection:text-white">

    <!-- =================================================================== -->
    <!-- NAVBAR FIXA ORGANIZADA EM LINHA ÚNICA (SEM QUEBRAS)                 -->
    <!-- =================================================================== -->
    <header class="sticky top-0 z-50 glass-navbar shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20 gap-4">
                <!-- Logo Limpa e Alinhada -->
                <a href="#topo" class="flex items-center gap-3 group shrink-0">
                    <div class="w-11 h-11 rounded-xl bg-oliva-700 flex items-center justify-center text-white shadow-md group-hover:bg-laranja-600 transition-colors">
                        <i class="fa-solid fa-seedling text-xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2 whitespace-nowrap">
                            <span class="font-display font-extrabold text-xl tracking-tight text-oliva-800">SUSTENTA<span class="text-laranja-600">+</span></span>
                            <span class="text-[11px] font-bold uppercase tracking-wider bg-laranja-100 text-laranja-700 px-2.5 py-0.5 rounded-full">Equipe 03</span>
                        </div>
                        <span class="text-[11px] text-stone-500 font-medium hidden sm:inline whitespace-nowrap">Alimentação Sustentável</span>
                    </div>
                </a>

                <!-- Links Desktop em Linha Única -->
                <nav class="hidden xl:flex items-center gap-6 text-sm font-semibold text-stone-700 whitespace-nowrap">
                    <a href="#producao-agua" class="hover:text-laranja-600 transition-colors">Impactos & Água</a>
                    <a href="#desperdicio-plastico" class="hover:text-laranja-600 transition-colors">Desperdício</a>
                    <a href="#vale-do-paraiba" class="hover:text-laranja-600 transition-colors">Vale do Paraíba</a>
                    <a href="#ferramentas-interativas" class="text-oliva-700 hover:text-laranja-600 transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-wand-magic-sparkles text-laranja-500"></i> Ferramentas
                    </a>
                    <a href="#ferramenta-receitas" class="hover:text-laranja-600 transition-colors">Receitas</a>
                    <a href="#ferramenta-mapa" class="hover:text-laranja-600 transition-colors">Mapa Taubaté</a>
                    <a href="#projeto-mundial" class="hover:text-laranja-600 transition-colors">Projeto Mundial</a>
                    <a href="#referencias" class="hover:text-laranja-600 transition-colors">Fontes</a>
                </nav>

                <!-- Botão Rápido Área ADM -->
                <div class="hidden xl:flex items-center shrink-0">
                    <a href="admin.php" class="inline-flex items-center gap-2 bg-oliva-700 hover:bg-oliva-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow transition-all whitespace-nowrap">
                        <i class="fa-solid fa-user-shield text-laranja-400"></i>
                        <span>Painel ADM</span>
                        <?php if ($total_pendentes_nav > 0): ?>
                            <span class="bg-laranja-500 text-white text-[10px] px-2 py-0.5 rounded-full font-extrabold">
                                <?= $total_pendentes_nav ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Botão Hambúrguer Mobile -->
                <button id="btn-mobile-menu" class="xl:hidden p-2.5 rounded-xl text-stone-700 hover:bg-stone-200/60" aria-label="Menu">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Menu Mobile -->
        <div id="mobile-menu" class="hidden xl:hidden bg-white border-b border-stone-200 px-4 pt-3 pb-6 space-y-2 shadow-lg">
            <a href="#producao-agua" class="block px-3 py-2 rounded-lg text-sm font-semibold text-stone-700 hover:bg-laranja-50">Produção & Pegada Hídrica</a>
            <a href="#desperdicio-plastico" class="block px-3 py-2 rounded-lg text-sm font-semibold text-stone-700 hover:bg-laranja-50">Transporte, Desperdício & Plásticos</a>
            <a href="#vale-do-paraiba" class="block px-3 py-2 rounded-lg text-sm font-semibold text-stone-700 hover:bg-laranja-50">Agricultura Familiar & Vale do Paraíba</a>
            <a href="#ferramentas-interativas" class="block px-3 py-2 rounded-lg text-sm font-semibold text-oliva-700 bg-oliva-50">Ferramentas Interativas</a>
            <a href="#ferramenta-receitas" class="block px-3 py-2 rounded-lg text-sm font-semibold text-stone-700 hover:bg-laranja-50">Repositório de Receitas</a>
            <a href="#ferramenta-mapa" class="block px-3 py-2 rounded-lg text-sm font-semibold text-stone-700 hover:bg-laranja-50">Mapa Sustentável de Taubaté</a>
            <a href="#projeto-mundial" class="block px-3 py-2 rounded-lg text-sm font-semibold text-stone-700 hover:bg-laranja-50">Projeto Mundial: Slow Food</a>
            <a href="#referencias" class="block px-3 py-2 rounded-lg text-sm font-semibold text-stone-700 hover:bg-laranja-50">Fontes e Referências</a>
            <div class="pt-2 border-t border-stone-100">
                <a href="admin.php" class="w-full flex items-center justify-center gap-2 bg-oliva-700 text-white text-sm font-bold py-2.5 rounded-xl">
                    <i class="fa-solid fa-lock"></i> Painel ADM (<?= $total_pendentes_nav ?> pendentes)
                </a>
            </div>
        </div>
    </header>

    <!-- Notificação Visual no Topo após envio POST -->
    <?php if ($alerta_js !== ''): ?>
    <div id="banner-notificacao" class="bg-oliva-700 text-white px-4 py-3.5 shadow-lg border-b border-oliva-600">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-check text-laranja-400 text-lg"></i>
                <span><?= htmlspecialchars($alerta_js) ?></span>
            </div>
            <button onclick="document.getElementById('banner-notificacao').remove()" class="text-white/80 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- =================================================================== -->
    <!-- HERO SECTION & QUESTÃO NORTEADORA                                   -->
    <!-- =================================================================== -->
    <section id="topo" class="hero-equipe03 text-white py-20 lg:py-28 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md border border-white/25 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest text-laranja-100">
                        <i class="fa-solid fa-leaf text-laranja-400"></i> Projeto SUSTENTA+ • Equipe 03
                    </div>
                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight">
                        Alimentação Sustentável: <span class="text-laranja-400">Do Campo ao Prato</span>
                    </h1>
                    <p class="text-lg sm:text-xl text-stone-100 leading-relaxed max-w-2xl">
                        Produzir, consumir e não desperdiçar. Compreenda o impacto real dos alimentos desde os solos e bacias hidrográficas até a mesa das famílias no Vale do Paraíba.
                    </p>
                    <div class="flex flex-wrap gap-4 pt-2">
                        <a href="#ferramenta-geladeira" class="inline-flex items-center gap-2.5 bg-laranja-600 hover:bg-laranja-500 text-white font-bold px-6 py-3.5 rounded-xl shadow-lg transition-all">
                            <i class="fa-solid fa-kitchen-set"></i> Abrir Geladeira Inteligente
                        </a>
                        <a href="#ferramenta-receitas" class="inline-flex items-center gap-2.5 bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 text-white font-bold px-6 py-3.5 rounded-xl transition-all">
                            <i class="fa-solid fa-utensils"></i> Ver Receitas Zero Desperdício
                        </a>
                    </div>
                </div>

                <!-- Bloco em Destaque da Questão Norteadora -->
                <div class="lg:col-span-5">
                    <div class="bg-white text-grafite rounded-3xl shadow-2xl overflow-hidden border-t-4 border-laranja-600">
                        <div class="h-40 w-full relative">
                            <img src="https://images.unsplash.com/photo-1490818387583-1baba5e638af?auto=format&fit=crop&w=900&q=80" alt="Alimentos frescos e sustentáveis" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-transparent"></div>
                            <span class="absolute bottom-3 left-6 text-[11px] font-extrabold uppercase tracking-wider bg-laranja-600 text-white px-3 py-1 rounded-full">
                                Questão Norteadora SUSTENTA+
                            </span>
                        </div>
                        <div class="p-6 sm:p-7">
                            <h2 class="font-display font-bold text-lg sm:text-xl text-grafite leading-snug mb-3">
                                "Como a informação pode transformar a nossa relação com a sustentabilidade?"
                            </h2>
                            <p class="text-sm text-stone-600 leading-relaxed mb-4">
                                No sistema alimentar, desperdiçamos aquilo que não compreendemos. Quando a informação revela que <strong>1 kg de carne bovina consome 15.415 litros de água</strong>[cite: 8] ou que <strong>cascas e talos possuem mais fibras e minerais que a polpa</strong>[cite: 8], o ato de cozinhar deixa de ser automático e passa a ser regenerativo.
                            </p>
                            <div class="bg-oliva-50 border border-oliva-100 rounded-2xl p-4 text-xs text-oliva-900 space-y-1.5">
                                <p>• <strong>Desvela o custo invisível:</strong> Mostra a água e a energia embutidas em cada alimento.</p>
                                <p>• <strong>Resgata nutrientes:</strong> Ensina o aproveitamento integral de cascas, talos e sobras.</p>
                                <p>• <strong>Fortalece o território:</strong> Conecta o cidadão às feiras locais de Taubaté e região.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dados Estatísticos Reais -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-14 pt-8 border-t border-white/20">
                <div class="bg-black/25 backdrop-blur-sm p-4 rounded-2xl border border-white/10">
                    <span class="text-2xl sm:text-3xl font-display font-extrabold text-laranja-400">1,3 Bilhão</span>
                    <p class="text-xs text-stone-200 mt-1">de toneladas de comida são perdidas ou desperdiçadas anualmente no planeta (FAO/ONU)[cite: 8].</p>
                </div>
                <div class="bg-black/25 backdrop-blur-sm p-4 rounded-2xl border border-white/10">
                    <span class="text-2xl sm:text-3xl font-display font-extrabold text-laranja-400">70%</span>
                    <p class="text-xs text-stone-200 mt-1">de toda a água doce captada globalmente é consumida pela cadeia agropecuária.</p>
                </div>
                <div class="bg-black/25 backdrop-blur-sm p-4 rounded-2xl border border-white/10">
                    <span class="text-2xl sm:text-3xl font-display font-extrabold text-laranja-400">27 Milhões</span>
                    <p class="text-xs text-stone-200 mt-1">de toneladas de alimentos vão para o lixo por ano no Brasil (Embrapa)[cite: 8].</p>
                </div>
                <div class="bg-black/25 backdrop-blur-sm p-4 rounded-2xl border border-white/10">
                    <span class="text-2xl sm:text-3xl font-display font-extrabold text-laranja-400">~70%</span>
                    <p class="text-xs text-stone-200 mt-1">dos alimentos in natura da mesa brasileira vêm da Agricultura Familiar (IBGE)[cite: 8].</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================== -->
    <!-- SEÇÃO 1: PRODUÇÃO DE ALIMENTOS, RECURSOS NATURAIS E PEGADA HÍDRICA  -->
    <!-- =================================================================== -->
    <section id="producao-agua" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="inline-block bg-oliva-100 text-oliva-800 text-xs font-extrabold uppercase tracking-widest px-3.5 py-1.5 rounded-full mb-3">
                Do Campo à Natureza
            </span>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-grafite">
                Produção de Alimentos, Recursos Naturais e Pegada Hídrica
            </h2>
            <p class="text-stone-600 mt-3 text-base">
                Como a agricultura e a pecuária moldam o uso da terra, o clima e as reservas de água doce do planeta.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-14">
            <article class="card-sustenta rounded-3xl overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="h-48 w-full relative img-zoom-container">
                        <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80" alt="Alimentação Sustentável e Vegetais" class="w-full h-full object-cover" loading="lazy">
                        <span class="absolute top-4 left-4 text-xs font-extrabold uppercase tracking-wider bg-oliva-700 text-white px-3 py-1 rounded-lg shadow">Conceito Base</span>
                    </div>
                    <div class="p-7 pb-4">
                        <h3 class="font-display font-bold text-xl text-grafite mb-3">O que é Alimentação Sustentável?</h3>
                        <p class="text-sm text-stone-600 leading-relaxed">
                            De acordo com a <strong>FAO/ONU</strong> e o <em>Guia Alimentar para a População Brasileira</em>, alimentação sustentável é aquela que nutre as gerações atuais e futuras com baixo impacto ambiental, protegendo a biodiversidade, valorizando a cultura alimentar regional e remunerando de forma justa quem produz.
                        </p>
                    </div>
                </div>
                <div class="px-7 pb-7">
                    <div class="bg-creme p-4 rounded-2xl border border-stone-200/70 text-xs space-y-1.5 text-stone-700">
                        <div class="font-bold text-oliva-800">Tripé da Comida Sustentável:</div>
                        <p>• Predominância de alimentos <em>in natura</em> e de origem vegetal.</p>
                        <p>• Respeito à sazonalidade (safra) e biodiversidade local.</p>
                        <p>• Redução drástica de ultraprocessados e desperdício.</p>
                    </div>
                </div>
            </article>

            <article class="card-sustenta rounded-3xl overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="h-48 w-full relative img-zoom-container">
                        <img src="https://images.unsplash.com/photo-1625246333195-78d9c38ad449?auto=format&fit=crop&w=800&q=80" alt="Produção Agrícola e Uso do Solo" class="w-full h-full object-cover" loading="lazy">
                        <span class="absolute top-4 left-4 text-xs font-extrabold uppercase tracking-wider bg-oliva-700 text-white px-3 py-1 rounded-lg shadow">Solo & Água</span>
                    </div>
                    <div class="p-7 pb-4">
                        <h3 class="font-display font-bold text-xl text-grafite mb-3">Produção de Alimentos e Uso de Recursos Naturais</h3>
                        <p class="text-sm text-stone-600 leading-relaxed">
                            Cerca de <strong>50% das terras habitáveis da Terra</strong> já são ocupadas pela agropecuária. O modelo agroindustrial intensivo consome enormes volumes de água subterrânea, combustíveis fósseis para maquinário e reservas minerais finitas (como fósforo e potássio) para fertilizantes químicos.
                        </p>
                    </div>
                </div>
                <div class="px-7 pb-7">
                    <div class="bg-creme p-4 rounded-2xl border border-stone-200/70 text-xs space-y-1.5 text-stone-700">
                        <div class="font-bold text-oliva-800">Dados FAO & Embrapa:</div>
                        <p>• <strong>33% dos solos globais</strong> estão degradados por erosão, salinização ou perda de matéria orgânica.</p>
                        <p>• Leva-se até <strong>500 anos</strong> para a natureza formar apenas 2,5 cm de solo fértil.</p>
                    </div>
                </div>
            </article>

            <article class="card-sustenta rounded-3xl overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="h-48 w-full relative img-zoom-container">
                        <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=800&q=80" alt="Impactos da Pecuária e Monocultura" class="w-full h-full object-cover" loading="lazy">
                        <span class="absolute top-4 left-4 text-xs font-extrabold uppercase tracking-wider bg-oliva-700 text-white px-3 py-1 rounded-lg shadow">Clima & Biomas</span>
                    </div>
                    <div class="p-7 pb-4">
                        <h3 class="font-display font-bold text-xl text-grafite mb-3">Impactos Ambientais da Agricultura e Pecuária</h3>
                        <p class="text-sm text-stone-600 leading-relaxed">
                            A conversão de florestas em pastagens e monoculturas extensivas (como soja e milho destinados à ração animal) é a principal causa de <strong>desmatamento e perda de habitat</strong> no Brasil. As monoculturas eliminam inimigos naturais de pragas, elevando o uso de agrotóxicos.
                        </p>
                    </div>
                </div>
                <div class="px-7 pb-7">
                    <div class="bg-creme p-4 rounded-2xl border border-stone-200/70 text-xs space-y-1.5 text-stone-700">
                        <div class="font-bold text-oliva-800">Emissões Climáticas no Campo:</div>
                        <p>• <strong>Metano (CH<sub>4</sub>):</strong> Gerado na digestão de ruminantes (pecuária), retém 28x mais calor que o CO<sub>2</sub>[cite: 8].</p>
                        <p>• <strong>Óxido Nitroso (N<sub>2</sub>O):</strong> Volatilizado por fertilizantes nitrogenados sintéticos.</p>
                    </div>
                </div>
            </article>
        </div>

        <!-- Infográfico Comparativo da Pegada Hídrica e Energética -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-md border border-stone-200">
            <div class="grid lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-5 space-y-4">
                    <span class="text-xs font-extrabold uppercase tracking-wider bg-laranja-600 text-white px-3 py-1 rounded-lg">Água Invisível no Prato</span>
                    <h3 class="font-display text-2xl sm:text-3xl font-extrabold text-grafite">
                        Pegada Hídrica e Energética: Carne vs. Vegetais
                    </h3>
                    <p class="text-sm text-stone-600 leading-relaxed">
                        A <strong>Pegada Hídrica</strong> contabiliza toda a água doce utilizada direta e indiretamente na produção de um alimento: a água da chuva (verde), a água de rios e aquíferos para irrigação (azul) e a água necessária para diluir poluentes (cinza).
                    </p>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div class="rounded-2xl overflow-hidden border border-red-200 bg-red-50">
                            <img src="https://images.unsplash.com/photo-1603048297172-c92544798d5a?auto=format&fit=crop&w=400&q=80" alt="1kg de Carne Bovina" class="w-full h-24 object-cover">
                            <div class="p-2.5 text-center">
                                <span class="text-xs font-extrabold text-red-700 block">1 kg de Carne Bovina</span>
                                <span class="text-[11px] text-stone-600">15.415 Litros de água[cite: 8]</span>
                            </div>
                        </div>
                        <div class="rounded-2xl overflow-hidden border border-emerald-200 bg-emerald-50">
                            <img src="https://images.unsplash.com/photo-1518843875459-f738682238a6?auto=format&fit=crop&w=400&q=80" alt="1kg de Vegetais e Legumes" class="w-full h-24 object-cover">
                            <div class="p-2.5 text-center">
                                <span class="text-xs font-extrabold text-emerald-800 block">1 kg de Hortaliças</span>
                                <span class="text-[11px] text-stone-600">~214 a 287 Litros[cite: 8]</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-laranja-50 border-l-4 border-laranja-600 p-4 rounded-r-2xl text-xs text-stone-700">
                        <p>Para produzir <strong>1 kg de proteína bovina</strong>, o animal consome dezenas de quilos de grãos irrigados ao longo de anos. Já hortaliças, raízes e leguminosas exigem <strong>até 70 vezes menos água</strong>.</p>
                    </div>
                </div>

                <div class="lg:col-span-7 space-y-4 bg-creme p-6 rounded-2xl border border-stone-200">
                    <div class="flex items-center justify-between text-xs font-bold text-stone-500 uppercase tracking-wider pb-2 border-b border-stone-200">
                        <span>Alimento (Produção de 1 kg)</span>
                        <span>Pegada Hídrica Total (Litros / kg)</span>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm font-bold mb-1">
                            <span>🥩 Carne Bovina</span>
                            <span class="text-red-700 font-extrabold">15.415 Litros[cite: 8]</span>
                        </div>
                        <div class="water-bar-track">
                            <div class="water-bar-fill bg-gradient-to-r from-red-600 to-laranja-600" style="width: 100%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm font-bold mb-1">
                            <span>🥓 Carne Suína</span>
                            <span class="text-laranja-700 font-extrabold">5.988 Litros</span>
                        </div>
                        <div class="water-bar-track">
                            <div class="water-bar-fill bg-laranja-500" style="width: 39%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm font-bold mb-1">
                            <span>🍗 Carne de Frango</span>
                            <span class="text-amber-700 font-extrabold">4.325 Litros</span>
                        </div>
                        <div class="water-bar-track">
                            <div class="water-bar-fill bg-amber-500" style="width: 28%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm font-bold mb-1">
                            <span>🍚 Arroz Beneficiado</span>
                            <span class="text-oliva-700 font-extrabold">2.497 Litros[cite: 8]</span>
                        </div>
                        <div class="water-bar-track">
                            <div class="water-bar-fill bg-oliva-600" style="width: 16%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm font-bold mb-1">
                            <span>🥔 Batata Inglesa</span>
                            <span class="text-emerald-700 font-extrabold">287 Litros</span>
                        </div>
                        <div class="water-bar-track">
                            <div class="water-bar-fill bg-emerald-500" style="width: 4%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm font-bold mb-1">
                            <span>🍅 Tomate, Cenoura e Folhosas</span>
                            <span class="text-emerald-700 font-extrabold">214 Litros[cite: 8]</span>
                        </div>
                        <div class="water-bar-track">
                            <div class="water-bar-fill bg-emerald-400" style="width: 3%"></div>
                        </div>
                    </div>

                    <p class="text-[11px] text-stone-500 pt-2 italic">
                        Fonte: Water Footprint Network (Mekonnen & Hoekstra) e Embrapa[cite: 8].
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================== -->
    <!-- SEÇÃO 2: LOGÍSTICA, DESPERDÍCIO E POLUIÇÃO POR EMBALAGENS           -->
    <!-- =================================================================== -->
    <section id="desperdicio-plastico" class="py-20 bg-oliva-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="inline-block bg-laranja-600 text-white text-xs font-extrabold uppercase tracking-widest px-3.5 py-1.5 rounded-full mb-3">
                    Desafios da Cadeia Alimentar
                </span>
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold">
                    Logística, Desperdício de Alimentos e Poluição por Embalagens
                </h2>
                <p class="text-stone-300 mt-3 text-base">
                    As perdas nas rodovias brasileiras, o paradoxo da fome frente ao lixo orgânico e o excesso de plásticos descartáveis.
                </p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-3xl overflow-hidden flex flex-col justify-between group">
                    <div>
                        <div class="h-48 w-full relative img-zoom-container">
                            <img src="https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?auto=format&fit=crop&w=800&q=80" alt="Transporte rodoviário de alimentos" class="w-full h-full object-cover" loading="lazy">
                            <span class="absolute top-4 left-4 text-xs font-bold uppercase tracking-wider bg-laranja-600 text-white px-3 py-1 rounded-lg shadow">Logística & Estradas</span>
                        </div>
                        <div class="p-7 pb-4">
                            <h3 class="font-display font-bold text-xl mb-3">Transporte e Distribuição (Food Miles)</h3>
                            <p class="text-sm text-stone-200 leading-relaxed">
                                O conceito de <strong>Food Miles</strong> mede a distância que a comida viaja da fazenda ao consumidor. No Brasil, a matriz rodoviária concentra o escoamento: caminhões percorrem milhares de quilômetros queimando diesel, com caixotes mal acondicionados e sem cadeia de frio.
                            </p>
                        </div>
                    </div>
                    <div class="px-7 pb-7">
                        <div class="bg-black/30 rounded-2xl p-4 text-xs text-laranja-200 border border-white/10">
                            <strong>Perdas nas Estradas (Embrapa):</strong> Do total desperdiçado no Brasil, <strong>50% perde-se no manuseio e transporte rodoviário</strong> e <strong>30% nas centrais de abastecimento (CEASAs)</strong>[cite: 8].
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-3xl overflow-hidden flex flex-col justify-between group">
                    <div>
                        <div class="h-48 w-full relative img-zoom-container">
    <img src="img/desperdicio.jpg" alt="Desperdício de frutas e hortaliças" class="w-full h-full object-cover" loading="lazy">
    <span class="absolute top-4 left-4 text-xs font-bold uppercase tracking-wider bg-laranja-600 text-white px-3 py-1 rounded-lg shadow">Desperdício Global</span>
</div>
                        <div class="p-7 pb-4">
                            <h3 class="font-display font-bold text-xl mb-3">O Escândalo do Desperdício no Brasil e no Mundo</h3>
                            <p class="text-sm text-stone-200 leading-relaxed">
                                Enquanto mais de 730 milhões de pessoas enfrentam insegurança alimentar grave no mundo, <strong>1/3 de toda a produção global (1,3 bilhão de toneladas)</strong> vai para o lixo (FAO)[cite: 8]. O Brasil está entre os 10 países que mais desperdiçam: os últimos 20% das perdas acontecem nos mercados e residências.
                            </p>
                        </div>
                    </div>
                    <div class="px-7 pb-7">
                        <div class="bg-black/30 rounded-2xl p-4 text-xs text-laranja-200 border border-white/10">
                            <strong>Impacto Climático:</strong> O desperdício global de comida responde por <strong>8% a 10% de todas as emissões humanas de gases de efeito estufa</strong> (PNUMA/ONU).
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-3xl overflow-hidden flex flex-col justify-between group">
                    <div>
                        <div class="h-48 w-full relative img-zoom-container">
                            <img src="https://images.unsplash.com/photo-1611284446314-60a58ac0deb9?auto=format&fit=crop&w=800&q=80" alt="Resíduos plásticos e embalagens descartáveis" class="w-full h-full object-cover" loading="lazy">
                            <span class="absolute top-4 left-4 text-xs font-bold uppercase tracking-wider bg-laranja-600 text-white px-3 py-1 rounded-lg shadow">Poluição Plástica</span>
                        </div>
                        <div class="p-7 pb-4">
                            <h3 class="font-display font-bold text-xl mb-3">Impacto das Embalagens Plásticas e Resíduos</h3>
                            <p class="text-sm text-stone-200 leading-relaxed">
                                Mais de <strong>40% dos plásticos produzidos no mundo são para embalagens de uso único</strong> — principalmente filmes plásticos, bandejas de isopor (EPS), sachês e garrafas PET usados por poucos minutos na alimentação, mas que permanecem por até 450 anos no ambiente gerando microplásticos.
                            </p>
                        </div>
                    </div>
                    <div class="px-7 pb-7">
                        <div class="bg-black/30 rounded-2xl p-4 text-xs text-laranja-200 border border-white/10">
                            <strong>Duplo Problema nos Aterros:</strong> Embalagens sujas com restos de comida perdem reciclabilidade, e os resíduos orgânicos soterrados geram chorume e metano[cite: 8].
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================== -->
    <!-- SEÇÃO 3: AGRICULTURA FAMILIAR E O VALE DO PARAÍBA                   -->
    <!-- =================================================================== -->
    <section id="vale-do-paraiba" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="inline-block bg-oliva-100 text-oliva-800 text-xs font-extrabold uppercase tracking-widest px-3.5 py-1.5 rounded-full mb-3">
                Soluções & Território Regional
            </span>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-grafite">
                Agricultura Familiar, Consumo Consciente e o Vale do Paraíba
            </h2>
            <p class="text-stone-600 mt-3 text-base">
                As soluções práticas que aproximam quem planta de quem consome na Bacia do Rio Paraíba do Sul e em Taubaté.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-14">
            <div class="card-sustenta rounded-3xl overflow-hidden">
                <div class="h-48 w-full relative img-zoom-container">
                    <img src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=800&q=80" alt="Agricultura Familiar e Colheita Local" class="w-full h-full object-cover" loading="lazy">
                    <span class="absolute top-4 left-4 text-xs font-extrabold uppercase bg-oliva-700 text-white px-3 py-1 rounded-lg shadow">Produção Familiar</span>
                </div>
                <div class="p-7">
                    <h3 class="font-display font-bold text-xl text-grafite mb-3">Alimentação Local e Agricultura Familiar</h3>
                    <p class="text-sm text-stone-600 leading-relaxed">
                        Segundo o Censo Agropecuário do IBGE, os pequenos produtores familiares representam <strong>77% dos estabelecimentos agrícolas do Brasil</strong> e produzem a maior parte da mandioca (87%), feijão (70%), hortaliças e frutas[cite: 8]. A produção em pequena escala diversifica culturas, preserva sementes crioulas e mantém o trabalhador no campo com dignidade.
                    </p>
                </div>
            </div>

            <div class="card-sustenta rounded-3xl overflow-hidden">
                <div class="h-48 w-full relative img-zoom-container">
                    <img src="https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=800&q=80" alt="Agroecologia e Mudas Sustentáveis" class="w-full h-full object-cover" loading="lazy">
                    <span class="absolute top-4 left-4 text-xs font-extrabold uppercase bg-oliva-700 text-white px-3 py-1 rounded-lg shadow">Agroecologia</span>
                </div>
                <div class="p-7">
                    <h3 class="font-display font-bold text-xl text-grafite mb-3">Iniciativas para Reduzir Impactos</h3>
                    <p class="text-sm text-stone-600 leading-relaxed">
                        Entre as principais iniciativas estão os <strong>Sistemas Agroflorestais (SAFs)</strong>, o manejo biológico de pragas, as compras públicas do <strong>PNAE</strong> (merenda escolar vinda de agricultores locais), os <strong>Bancos de Alimentos</strong> municipais e o incentivo à compostagem doméstica e comunitária para fechar o ciclo de nutrientes do solo.
                    </p>
                </div>
            </div>

            <div class="card-sustenta rounded-3xl overflow-hidden">
                <div class="h-48 w-full relative img-zoom-container">
                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80" alt="Consumidor Consciente na Feira Livre" class="w-full h-full object-cover" loading="lazy">
                    <span class="absolute top-4 left-4 text-xs font-extrabold uppercase bg-oliva-700 text-white px-3 py-1 rounded-lg shadow">Hábitos Diários</span>
                </div>
                <div class="p-7">
                    <h3 class="font-display font-bold text-xl text-grafite mb-3">Hábitos do Consumidor Consciente</h3>
                    <p class="text-sm text-stone-600 leading-relaxed">
                        Transformar a relação com a comida exige hábitos diários: planejar o cardápio semanal antes das compras, levar sacolas retornáveis (ecobags) à feira, armazenar corretamente folhas e grãos, cozinhar integralmente (cascas, talos e sementes), reduzir o desperdício no prato e compostar os resíduos crus.
                    </p>
                </div>
            </div>
        </div>

        <!-- DESTAQUE REGIONAL: BACIA DO PARAÍBA DO SUL & TAUBATÉ -->
        <div class="bg-gradient-to-br from-oliva-800 via-oliva-700 to-oliva-900 text-white rounded-3xl p-8 sm:p-10 shadow-xl">
            <div class="grid lg:grid-cols-12 gap-8 items-center mb-8">
                <div class="lg:col-span-8 space-y-4">
                    <div class="inline-flex items-center gap-2 bg-laranja-500 text-white text-xs font-extrabold uppercase tracking-wider px-3.5 py-1 rounded-full">
                        <i class="fa-solid fa-map-pin"></i> Realidade Regional • Taubaté e Vale do Paraíba
                    </div>
                    <h3 class="font-display text-2xl sm:text-3xl font-extrabold">
                        A Produção Agrícola na Bacia do Rio Paraíba do Sul e as Feiras Livres de Taubaté
                    </h3>
                    <p class="text-stone-100 text-sm sm:text-base leading-relaxed">
                        A <strong>Bacia Hidrográfica do Rio Paraíba do Sul</strong> — situada no eixo entre a Serra da Mantiqueira e a Serra do Mar — é uma das mais importantes do Sudeste brasileiro. Em <strong>Taubaté, Tremembé e Pindamonhangaba</strong>, as várzeas férteis do rio formaram um polo tradicional de <strong>rizicultura (arroz irrigado e arrozes especiais)</strong> e pecuária leiteira[cite: 8].
                    </p>
                    <p class="text-stone-200 text-sm leading-relaxed">
                        Na zona rural de Taubaté — em bairros históricos como <em>Registro, Monjolinho, Sete Voltas, Paiol e Quiririm</em> (na bacia do Rio Una, principal manancial que abastece a cidade) — centenas de famílias agricultoras cultivam hortaliças, mandioca, milho, banana, feijão e mel[cite: 8]. Quando o morador de Taubaté compra no <strong>Mercado Municipal (Mercatau)</strong> ou na <strong>Feira do Produtor Rural</strong>, ele apoia diretamente a conservação das águas da nossa bacia e elimina centenas de quilômetros de transporte rodoviário[cite: 8].
                    </p>
                </div>

                <div class="lg:col-span-4">
                    <div class="bg-white text-grafite p-6 rounded-2xl shadow-lg space-y-3">
                        <h4 class="font-display font-bold text-base text-oliva-800 border-b border-stone-200 pb-2">
                            <i class="fa-solid fa-wheat-awn text-laranja-600 mr-1.5"></i> Sabores & Sustentabilidade Local
                        </h4>
                        <p class="text-xs text-stone-600 leading-relaxed">
                            <strong>• Arroz do Vale:</strong> Produzido nas várzeas de Taubaté/Tremembé com tradição secular[cite: 8].
                        </p>
                        <p class="text-xs text-stone-600 leading-relaxed">
                            <strong>• Feiras de Bairro e Mercatau:</strong> Alimentos frescos sem bandejas de isopor ou filme plástico[cite: 8].
                        </p>
                        <p class="text-xs text-stone-600 leading-relaxed">
                            <strong>• Bacia do Rio Una:</strong> Sistemas agroflorestais em Taubaté protegem as nascentes que abastecem a população urbana.
                        </p>
                        <a href="#ferramenta-mapa" class="block text-center bg-laranja-600 hover:bg-laranja-700 text-white text-xs font-bold py-3 rounded-xl transition-colors mt-2">
                            Explorar Mapa Sustentável de Taubaté →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Galeria Visual do Vale do Paraíba e Taubaté -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-6 border-t border-white/15">
                <div class="bg-black/25 rounded-2xl overflow-hidden border border-white/10 group">
                    <div class="h-36 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?auto=format&fit=crop&w=600&q=80" alt="Rizicultura no Vale do Paraíba" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    </div>
                    <div class="p-3.5">
                        <span class="text-[10px] font-bold uppercase text-laranja-400">Várzea do Paraíba</span>
                        <h5 class="font-display font-bold text-sm">Tradição do Arroz no Vale</h5>
                    </div>
                </div>
                <div class="bg-black/25 rounded-2xl overflow-hidden border border-white/10 group">
                    <div class="h-36 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=600&q=80" alt="Feiras Livres de Taubaté" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    </div>
                    <div class="p-3.5">
                        <span class="text-[10px] font-bold uppercase text-laranja-400">Circuitos Curtos</span>
                        <h5 class="font-display font-bold text-sm">Mercatau & Feiras Livres</h5>
                    </div>
                </div>
                <div class="bg-black/25 rounded-2xl overflow-hidden border border-white/10 group">
                    <div class="h-36 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=600&q=80" alt="Zona Rural de Taubaté e Mantiqueira" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    </div>
                    <div class="p-3.5">
                        <span class="text-[10px] font-bold uppercase text-laranja-400">Bacia do Rio Una</span>
                        <h5 class="font-display font-bold text-sm">Zona Rural de Taubaté</h5>
                    </div>
                </div>
                <div class="bg-black/25 rounded-2xl overflow-hidden border border-white/10 group">
                    <div class="h-36 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1597362925123-77861d3fbac7?auto=format&fit=crop&w=600&q=80" alt="Hortaliças Orgânicas Regionais" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    </div>
                    <div class="p-3.5">
                        <span class="text-[10px] font-bold uppercase text-laranja-400">Agroecologia</span>
                        <h5 class="font-display font-bold text-sm">Cestas Direto da Roça</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =================================================================== -->
    <!-- SEÇÃO DAS FERRAMENTAS PRÁTICAS E INTERATIVAS                        -->
    <!-- =================================================================== -->
    <div id="ferramentas-interativas" class="bg-stone-100 border-y border-stone-200 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-24">

            <!-- =========================================================== -->
            <!-- MITOS OU VERDADES DA ALIMENTAÇÃO SUSTENTÁVEL (10 DE 30)     -->
            <!-- =========================================================== -->
            <section id="ferramenta-mitos">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 bg-laranja-100 text-laranja-700 text-xs font-extrabold uppercase tracking-wider px-3 py-1 rounded-full mb-2">
                            <i class="fa-solid fa-bolt"></i> Quiz Interativo • Banco de <?= count($DADOS_MITOS) ?> Questões
                        </span>
                        <h2 class="font-display text-3xl font-extrabold text-grafite">
                            Mitos ou Verdades da Alimentação Sustentável
                        </h2>
                        <p class="text-stone-600 text-sm mt-1">
                            Responda às <strong>10 perguntas sorteadas</strong> do nosso banco de 30 questões. Clique em <strong>"Reiniciar"</strong> para sortear novas perguntas!
                        </p>
                    </div>
                    <div class="bg-white px-5 py-3 rounded-2xl shadow-sm border border-stone-200 flex items-center gap-4 shrink-0">
                        <span class="text-xs font-bold text-stone-500 uppercase">Acertos:</span>
                        <span class="font-display font-extrabold text-xl text-oliva-700">
                            <span id="placar-acertos">0</span> / 10
                        </span>
                        <button type="button" onclick="resetarMitos()" class="text-xs bg-laranja-50 hover:bg-laranja-100 text-laranja-700 px-3 py-1.5 rounded-xl font-bold transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-rotate-right"></i> Reiniciar (Sortear 10)
                        </button>
                    </div>
                </div>

                <!-- Grid de 2 Colunas (5 linhas x 2 colunas = 10 cards perfeitamente encaixados) -->
                <div id="grid-mitos" class="grid md:grid-cols-2 gap-5"></div>
            </section>

            <!-- =========================================================== -->
            <!-- GELADEIRA INTELIGENTE — ZERO DESPERDÍCIO                    -->
            <!-- =========================================================== -->
            <section id="ferramenta-geladeira" class="bg-white rounded-3xl p-7 sm:p-10 shadow-md border border-stone-200">
                <div class="max-w-3xl mb-8">
                    <span class="inline-flex items-center gap-1.5 bg-oliva-100 text-oliva-800 text-xs font-extrabold uppercase tracking-wider px-3 py-1 rounded-full mb-2">
                        <i class="fa-solid fa-kitchen-set"></i> Simulador Antidesperdício
                    </span>
                    <h2 class="font-display text-3xl font-extrabold text-grafite">
                        Geladeira Inteligente — Zero Desperdício
                    </h2>
                    <p class="text-stone-600 text-sm mt-1">
                        Marque os ingredientes que estão sobrando na sua casa. O sistema cruza com as receitas sustentáveis e calcula o desperdício evitado!
                    </p>
                </div>

                <div class="mb-8">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                        <?php foreach ($DADOS_GELADEIRA as $ing): ?>
                        <label class="ingrediente-pill flex items-center gap-2.5 p-3 rounded-xl border border-stone-200 bg-creme hover:border-laranja-500 cursor-pointer select-none">
                            <input type="checkbox" value="<?= htmlspecialchars($ing['nome']) ?>" onchange="atualizarGeladeira()" class="chk-ingrediente w-4 h-4 accent-laranja-600 rounded">
                            <i class="fa-solid <?= htmlspecialchars($ing['icone']) ?> text-oliva-700 text-sm"></i>
                            <span class="text-xs sm:text-sm font-semibold text-stone-800"><?= htmlspecialchars($ing['nome']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 items-center bg-stone-50 p-4 rounded-2xl border border-stone-200">
                        <div class="flex-1 w-full relative">
                            <i class="fa-solid fa-magnifying-glass-plus absolute left-3.5 top-3.5 text-stone-400"></i>
                            <input type="text" id="input-ingrediente-extra" oninput="atualizarGeladeira()" placeholder="Digite outro ingrediente que tem em casa (ex: alecrim, alho, queijo, canela)..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:border-laranja-600">
                        </div>
                        <button type="button" onclick="limparGeladeira()" class="w-full sm:w-auto px-4 py-2.5 text-xs font-bold text-stone-600 hover:text-red-600 bg-white border border-stone-300 rounded-xl transition-colors">
                            <i class="fa-solid fa-eraser mr-1"></i> Limpar Seleção
                        </button>
                    </div>
                </div>

                <div id="resumo-impacto-geladeira" class="hidden mb-6 bg-oliva-50 border border-oliva-200 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-oliva-700 text-white flex items-center justify-center">
                            <i class="fa-solid fa-scale-balanced"></i>
                        </div>
                        <div>
                            <h4 class="font-display font-bold text-sm text-oliva-900" id="texto-compatibilidade-geladeira"></h4>
                            <p class="text-xs text-oliva-700" id="texto-desperdicio-evitado"></p>
                        </div>
                    </div>
                    <span class="bg-laranja-600 text-white text-xs font-bold px-3.5 py-1.5 rounded-xl" id="badge-agua-salva"></span>
                </div>

                <div id="resultado-geladeira" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
            </section>

            <!-- =========================================================== -->
            <!-- GUIA DE PLANTIO REGIONAL + COMPOSTAGEM ALINHADA (STICKY)    -->
            <!-- =========================================================== -->
            <section id="ferramenta-plantio" class="bg-white rounded-3xl p-7 sm:p-10 shadow-md border border-stone-200">
                <div class="grid lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-8">
                        <span class="inline-flex items-center gap-1.5 bg-laranja-100 text-laranja-700 text-xs font-extrabold uppercase tracking-wider px-3 py-1 rounded-full mb-2">
                            <i class="fa-solid fa-sun-plant-wilt"></i> Cultivo Doméstico • Vale do Paraíba
                        </span>
                        <h2 class="font-display text-3xl font-extrabold text-grafite">
                            Guia de Plantio Regional — O Que Plantar em Casa?
                        </h2>
                        <p class="text-stone-600 text-sm mt-1 mb-6">
                            Selecione a <strong>Estação do Ano</strong> e o <strong>Espaço Disponível</strong> para receber recomendações ilustradas ajustadas ao clima de Taubaté e região.
                        </p>

                        <div class="grid sm:grid-cols-2 gap-4 bg-creme p-5 rounded-2xl border border-stone-200 mb-6">
                            <div>
                                <label class="block text-xs font-bold uppercase text-stone-600 mb-1.5">
                                    <i class="fa-solid fa-calendar-days text-laranja-600 mr-1"></i> Época do Ano / Estação:
                                </label>
                                <select id="select-estacao" onchange="filtrarPlantio()" class="w-full p-3 rounded-xl border border-stone-300 bg-white text-sm font-semibold focus:outline-none focus:border-laranja-600">
                                    <option value="Primavera">Primavera (Setembro a Dezembro)</option>
                                    <option value="Verao">Verão (Dezembro a Março — Quente e Chuvoso)</option>
                                    <option value="Outono">Outono (Março a Junho — Clima Ameno)</option>
                                    <option value="Inverno">Inverno (Junho a Setembro — Seco e Frio)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-stone-600 mb-1.5">
                                    <i class="fa-solid fa-maximize text-oliva-700 mr-1"></i> Espaço Disponível:
                                </label>
                                <select id="select-espaco" onchange="filtrarPlantio()" class="w-full p-3 rounded-xl border border-stone-300 bg-white text-sm font-semibold focus:outline-none focus:border-laranja-600">
                                    <option value="Apartamento/Vasos pequenos">Apartamento / Vasos pequenos</option>
                                    <option value="Quintal/Canteiro">Quintal / Canteiro</option>
                                    <option value="Horta Escolar">Horta Escolar</option>
                                </select>
                            </div>
                        </div>

                        <div id="grid-plantio" class="grid sm:grid-cols-2 gap-4"></div>
                    </div>

                    <!-- Coluna Lateral Fixa (Sticky) com Compostagem + Adubos Naturais -->
                    <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-6">
                        <div class="bg-oliva-800 text-white rounded-3xl overflow-hidden shadow-lg">
                            <div class="h-40 w-full relative">
                                <img src="https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?auto=format&fit=crop&w=700&q=80" alt="Compostagem Caseira e Terra Fértil" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-oliva-900 via-oliva-900/40 to-transparent"></div>
                                <div class="absolute bottom-3 left-5 right-5 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-laranja-500 text-white flex items-center justify-center shadow shrink-0">
                                        <i class="fa-solid fa-recycle"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] uppercase tracking-wider text-laranja-300 font-bold block">Adubo Orgânico</span>
                                        <h3 class="font-display font-bold text-base leading-tight">Como Fazer Compostagem Caseira</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 space-y-3">
                                <p class="text-xs text-stone-200 leading-relaxed">
                                    Transforme cascas e restos de cozinha em húmus fértil para os seus vasos seguindo 4 passos simples:
                                </p>
                                <ol class="space-y-2.5 text-xs">
                                    <li class="bg-white/10 p-3 rounded-xl border border-white/10">
                                        <strong class="text-laranja-300 block mb-0.5">Passo 1: Escolha a Composteira</strong>
                                        Use baldes empilháveis em apartamento ou um canto sombreado e arejado no quintal.
                                    </li>
                                    <li class="bg-white/10 p-3 rounded-xl border border-white/10">
                                        <strong class="text-laranja-300 block mb-0.5">Passo 2: Intercale Camadas (1:2)</strong>
                                        Para cada 1 porção de resíduos úmidos (cascas de frutas, legumes, borra de café), cubra com 2 porções de matéria seca (folhas secas ou serragem sem verniz).
                                    </li>
                                    <li class="bg-white/10 p-3 rounded-xl border border-white/10">
                                        <strong class="text-laranja-300 block mb-0.5">Passo 3: Evite Itens Proibidos</strong>
                                        Não coloque restos de carnes, laticínios, óleos ou gorduras para evitar insetos e mau cheiro.
                                    </li>
                                    <li class="bg-white/10 p-3 rounded-xl border border-white/10">
                                        <strong class="text-laranja-300 block mb-0.5">Passo 4: Colha o Húmus (60 a 90 dias)</strong>
                                        Em 2 a 3 meses você terá terra preta perfumada e biofertilizante líquido!
                                    </li>
                                </ol>
                            </div>
                        </div>

                        <!-- Quadro Complementar de Adubos Rápidos de Cozinha -->
                        <div class="bg-creme rounded-3xl p-6 border border-stone-200 shadow-sm space-y-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-laranja-100 text-laranja-600 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-flask"></i>
                                </span>
                                <h4 class="font-display font-bold text-base text-grafite">Biofertilizantes Rápidos de Cozinha</h4>
                            </div>
                            <ul class="space-y-2 text-xs text-stone-600 leading-relaxed">
                                <li>• <strong>Farinha de Casca de Ovo:</strong> Seque as cascas ao sol e bata no liquidificador. Rica em cálcio para tomateiros e pimentões.</li>
                                <li>• <strong>Água de Casca de Banana:</strong> Ferva 3 cascas em 1 litro de água, deixe esfriar e regue os vasos para fornecer potássio e fósforo.</li>
                                <li>• <strong>Borra de Café Seca:</strong> Misture na terra superficial dos vasos de folhosas para fornecer nitrogênio natural.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================== -->
            <!-- REPOSITÓRIO DE RECEITAS SUSTENTÁVEIS (27 RECEITAS = 9x3)    -->
            <!-- =========================================================== -->
            <section id="ferramenta-receitas">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 bg-oliva-100 text-oliva-800 text-xs font-extrabold uppercase tracking-wider px-3 py-1 rounded-full mb-2">
                            <i class="fa-solid fa-utensils"></i> Culinária Sustentável • Pensamento Verde, eConsciente & Receiteria
                        </span>
                        <h2 class="font-display text-3xl font-extrabold text-grafite">
                            Repositório Dinâmico de Receitas Sustentáveis
                        </h2>
                        <p class="text-stone-600 text-sm mt-1">
                            Receitas selecionadas de aproveitamento integral e reaproveitamento de sobras. Filtre em tempo real, envie sua receita ou deixe uma avaliação!
                        </p>
                    </div>
                    <button type="button" onclick="abrirModalNovaReceita()" class="inline-flex items-center justify-center gap-2 bg-laranja-600 hover:bg-laranja-700 text-white text-sm font-bold px-5 py-3 rounded-xl shadow transition-all shrink-0">
                        <i class="fa-solid fa-plus-circle"></i> Enviar Minha Receita Sustentável
                    </button>
                </div>

                <!-- Busca em Tempo Real e Filtros de Categoria -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-stone-200 mb-8 space-y-4">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-stone-400"></i>
                        <input type="text" id="busca-receitas" oninput="filtrarRepositorioReceitas()" placeholder="Pesquisar receita por nome ou ingrediente (ex: casca de banana, sobras de arroz, batata, rama de cenoura, pão)..." class="w-full pl-11 pr-4 py-3 rounded-xl border border-stone-300 text-sm focus:outline-none focus:border-laranja-600">
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="selecionarCategoriaReceita('Todas')" data-cat="Todas" class="btn-cat-receita bg-oliva-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all">
                            Todas (<?= count($receitas_db) ?>)
                        </button>
                        <button type="button" onclick="selecionarCategoriaReceita('Aproveitamento Integral')" data-cat="Aproveitamento Integral" class="btn-cat-receita bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold px-4 py-2 rounded-xl transition-all">
                            Aproveitamento Integral
                        </button>
                        <button type="button" onclick="selecionarCategoriaReceita('Vegana/Vegetariana')" data-cat="Vegana/Vegetariana" class="btn-cat-receita bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold px-4 py-2 rounded-xl transition-all">
                            Vegana/Vegetariana
                        </button>
                        <button type="button" onclick="selecionarCategoriaReceita('Rápida & Econômica')" data-cat="Rápida & Econômica" class="btn-cat-receita bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold px-4 py-2 rounded-xl transition-all">
                            Rápida & Econômica
                        </button>
                        <button type="button" onclick="selecionarCategoriaReceita('Tradicional Regional')" data-cat="Tradicional Regional" class="btn-cat-receita bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold px-4 py-2 rounded-xl transition-all">
                            Tradicional Regional
                        </button>
                    </div>
                </div>

<!-- Grid de Receitas (Exibe apenas 6 inicialmente via JS) -->
                <div id="grid-repositorio-receitas" class="grid md:grid-cols-2 lg:grid-cols-3 gap-7">
                    <?php foreach ($receitas_db as $rec):
                        $qtd_com = count($rec['comentarios']);
                        $soma = 0;
                        foreach ($rec['comentarios'] as $cm) { $soma += (int)$cm['nota']; }
                        $media = $qtd_com > 0 ? round($soma / $qtd_com, 1) : 5.0;
                        $img_rec = !empty($rec['imagem_url']) ? $rec['imagem_url'] : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80';
                    ?>
                    <article class="card-receita-item card-sustenta rounded-3xl overflow-hidden flex flex-col justify-between"
                             data-titulo="<?= htmlspecialchars(mb_strtolower($rec['titulo'])) ?>"
                             data-ingredientes="<?= htmlspecialchars(mb_strtolower($rec['ingredientes'])) ?>"
                             data-categoria="<?= htmlspecialchars($rec['categoria']) ?>">
                        <div>
                            <div class="h-48 w-full relative img-zoom-container bg-stone-200">
                                <img src="<?= htmlspecialchars($img_rec) ?>"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80';"
                                     alt="<?= htmlspecialchars($rec['titulo']) ?>"
                                     class="w-full h-full object-cover" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent"></div>
                                <span class="absolute top-3 left-3 text-[11px] font-extrabold uppercase tracking-wider bg-oliva-700 text-white px-3 py-1 rounded-full shadow">
                                    <?= htmlspecialchars($rec['categoria']) ?>
                                </span>
                                <span class="absolute top-3 right-3 bg-white/95 text-grafite text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1 shadow">
                                    <i class="fa-solid fa-star text-amber-500"></i> <?= number_format($media, 1, ',', '.') ?>
                                </span>
                                <span class="absolute bottom-2.5 left-3.5 bg-black/65 backdrop-blur-sm text-white text-[11px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                                    <i class="fa-solid fa-utensils text-laranja-400"></i> <?= htmlspecialchars($rec['autor']) ?>
                                </span>
                            </div>

                            <div class="p-6 pb-4">
                                <h3 class="font-display font-bold text-xl text-grafite mb-2 leading-snug">
                                    <?= htmlspecialchars($rec['titulo']) ?>
                                </h3>

                                <div class="mb-4">
                                    <span class="text-[11px] font-bold uppercase text-stone-400 block mb-1">Ingredientes Principais:</span>
                                    <p class="text-xs text-stone-600 line-clamp-2 leading-relaxed">
                                        <?= htmlspecialchars($rec['ingredientes']) ?>
                                    </p>
                                </div>

                                <div class="bg-laranja-50 border border-laranja-100 p-3 rounded-2xl">
                                    <span class="text-[11px] font-extrabold uppercase text-laranja-700 block mb-0.5">
                                        <i class="fa-solid fa-earth-americas mr-1"></i> Impacto Positivo:
                                    </span>
                                    <p class="text-xs text-stone-700 leading-relaxed line-clamp-3">
                                        <?= htmlspecialchars($rec['dica_sustentavel']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 pb-6 pt-2">
                            <button type="button" onclick="abrirModalReceita(<?= (int)$rec['id'] ?>)" class="w-full bg-oliva-700 hover:bg-laranja-600 text-white text-xs font-bold py-3 px-4 rounded-xl transition-colors flex items-center justify-center gap-2">
                                <i class="fa-solid fa-book-open"></i> Modo de Preparo & Avaliações (<?= $qtd_com ?>)
                            </button>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Botões de Paginação ("Ver Mais" / "Mostrar Menos") -->
                <div id="container-paginacao-receitas" class="mt-10 flex flex-col items-center justify-center gap-3">
                    <p id="contador-receitas-visiveis" class="text-xs font-bold uppercase tracking-wider text-stone-500"></p>
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <button type="button" id="btn-ver-mais-receitas" onclick="carregarMaisReceitas()"
                                class="inline-flex items-center gap-2 bg-laranja-600 hover:bg-laranja-700 text-white text-sm font-bold px-7 py-3.5 rounded-2xl shadow-md transition-all">
                            <i class="fa-solid fa-plus-circle"></i>
                            <span id="texto-btn-ver-mais">Ver Mais Receitas</span>
                        </button>
                        <button type="button" id="btn-mostrar-menos-receitas" onclick="recolherReceitas()"
                                class="hidden inline-flex items-center gap-2 bg-white hover:bg-stone-100 text-stone-700 border border-stone-300 text-xs font-bold px-5 py-3.5 rounded-2xl shadow-sm transition-all">
                            <i class="fa-solid fa-chevron-up text-oliva-700"></i> Mostrar Menos
                        </button>
                    </div>
                </div>

                <!-- Botão Final para Enviar Receita (Indo para moderação do Admin) -->
                <div class="mt-12 bg-oliva-800 text-white rounded-3xl p-8 text-center shadow-lg flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="text-left">
                        <span class="text-xs font-extrabold uppercase text-laranja-400 tracking-wider">Colabore com o Projeto</span>
                        <h3 class="font-display font-bold text-xl mt-1">Tem uma receita sustentável ou de aproveitamento integral?</h3>
                        <p class="text-xs text-stone-200 mt-0.5">Envie sua criação! O administrador do site fará a revisão antes de publicá-la.</p>
                    </div>
                    <button type="button" onclick="abrirModalNovaReceita()" class="bg-laranja-600 hover:bg-laranja-700 text-white text-sm font-bold px-6 py-3.5 rounded-xl shadow shrink-0 transition-colors">
                        <i class="fa-solid fa-plus-circle mr-1.5"></i> Enviar Minha Receita
                    </button>
                </div>
            </section>

            <!-- =========================================================== -->
            <!-- MAPA E GUIA DE SUSTENTABILIDADE NO VALE DO PARAÍBA          -->
            <!-- =========================================================== -->
            <section id="ferramenta-mapa">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 bg-laranja-100 text-laranja-700 text-xs font-extrabold uppercase tracking-wider px-3 py-1 rounded-full mb-2">
                            <i class="fa-solid fa-map-location-dot"></i> Guia Local Interativo • Taubaté e Região
                        </span>
                        <h2 class="font-display text-3xl font-extrabold text-grafite">
                            Mapa e Guia de Sustentabilidade no Vale do Paraíba e Taubaté
                        </h2>
                        <p class="text-stone-600 text-sm mt-1">
                            Filtre por cidade ou tipo de iniciativa, atualize o mapa interativo com 1 clique e envie avaliações sobre os pontos locais.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <select id="filtro-cidade-mapa" onchange="filtrarLocaisMapa()" class="px-3.5 py-2.5 rounded-xl border border-stone-300 bg-white text-xs font-bold text-stone-700">
                            <option value="Todas">Todas as Cidades</option>
                            <option value="Taubaté">Taubaté</option>
                            <option value="Tremembé">Tremembé</option>
                            <option value="Pindamonhangaba">Pindamonhangaba</option>
                            <option value="São José dos Campos">São José dos Campos</option>
                        </select>
                        <select id="filtro-tipo-mapa" onchange="filtrarLocaisMapa()" class="px-3.5 py-2.5 rounded-xl border border-stone-300 bg-white text-xs font-bold text-stone-700">
                            <option value="Todos">Todos os Tipos</option>
                            <option value="Mercado">Mercados</option>
                            <option value="Feira">Feiras Orgânicas / Produtor</option>
                            <option value="Horta">Hortas Comunitárias</option>
                            <option value="Projeto">Projetos Ambientais</option>
                            <option value="Cooperativa">Cooperativas & Agroecologia</option>
                        </select>
                    </div>
                </div>

                <div class="grid lg:grid-cols-12 gap-8 items-start">
                    <!-- Cards dos Locais -->
                    <div class="lg:col-span-7 space-y-5">
                        <?php foreach ($locais_db as $loc):
                            $qtd_com_loc = count($loc['comentarios']);
                            $img_loc = !empty($loc['imagem_url']) ? $loc['imagem_url'] : 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=800&q=80';
                        ?>
                        <div class="card-local-item card-sustenta rounded-3xl overflow-hidden"
                             data-cidade="<?= htmlspecialchars($loc['cidade']) ?>"
                             data-tipo="<?= htmlspecialchars($loc['tipo']) ?>">
                            <div class="grid sm:grid-cols-12">
                                <div class="sm:col-span-4 h-48 sm:h-auto img-zoom-container relative bg-stone-200">
                                    <img src="<?= htmlspecialchars($img_loc) ?>"
                                         onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=800&q=80';"
                                         alt="<?= htmlspecialchars($loc['nome']) ?>"
                                         class="w-full h-full object-cover" loading="lazy">
                                </div>
                                <div class="sm:col-span-8 p-6 flex flex-col justify-between">
                                    <div>
                                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider bg-oliva-700 text-white px-2.5 py-1 rounded-full">
                                                <?= htmlspecialchars($loc['tipo']) ?>
                                            </span>
                                            <span class="text-xs font-bold text-laranja-600 bg-laranja-50 px-2.5 py-1 rounded-full">
                                                <i class="fa-solid fa-location-dot mr-1"></i> <?= htmlspecialchars($loc['cidade']) ?> - SP
                                            </span>
                                        </div>

                                        <h3 class="font-display font-bold text-lg sm:text-xl text-grafite mb-2">
                                            <?= htmlspecialchars($loc['nome']) ?>
                                        </h3>

                                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mb-3">
                                            <?= htmlspecialchars($loc['descricao']) ?>
                                        </p>

                                        <p class="text-xs text-stone-500 mb-4 flex items-center gap-1.5">
                                            <i class="fa-solid fa-map-pin text-laranja-600"></i>
                                            <span><strong>Endereço:</strong> <?= htmlspecialchars($loc['endereco']) ?></span>
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-stone-100">
                                        <button type="button"
                                                onclick="atualizarMapaIframe('<?= htmlspecialchars(addslashes($loc['nome'] . ', ' . $loc['cidade'] . ', SP')) ?>', '<?= htmlspecialchars(addslashes($loc['nome'])) ?>')"
                                                class="bg-oliva-700 hover:bg-oliva-800 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-crosshairs text-laranja-400"></i> Ver no Mapa
                                        </button>

                                        <a href="<?= htmlspecialchars($loc['link_maps']) ?>" target="_blank" rel="noopener noreferrer"
                                           class="bg-laranja-600 hover:bg-laranja-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-diamond-turn-right"></i> Traçar Rota
                                        </a>

                                        <button type="button"
                                                onclick="abrirModalLocalComentarios(<?= (int)$loc['id'] ?>)"
                                                class="bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold px-3 py-2 rounded-xl transition-colors flex items-center gap-1.5 ml-auto">
                                            <i class="fa-solid fa-comments text-oliva-700"></i> Avaliações (<?= $qtd_com_loc ?>)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Coluna Direita Fixa (Sticky): Mapa Ampliado + Dicas de Feira Sustentável -->
                    <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-6">
                        <div class="bg-white p-5 rounded-3xl shadow-md border border-stone-200">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <span class="text-[11px] font-bold uppercase text-laranja-600 block">Visualização Interativa</span>
                                    <h4 id="titulo-mapa-ativo" class="font-display font-bold text-base text-grafite">Taubaté e Região, SP</h4>
                                </div>
                                <span class="w-9 h-9 rounded-xl bg-oliva-100 text-oliva-700 flex items-center justify-center">
                                    <i class="fa-solid fa-compass"></i>
                                </span>
                            </div>

                            <div class="rounded-2xl overflow-hidden border border-stone-200 bg-stone-100 h-80 sm:h-96">
                                <iframe id="iframe-mapa-taubate"
                                        src="https://maps.google.com/maps?q=Mercado+Municipal+de+Taubate,SP&t=&z=14&ie=UTF8&iwloc=&output=embed"
                                        class="w-full h-full border-0"
                                        allowfullscreen=""
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                        title="Mapa Interativo de Taubaté"></iframe>
                            </div>

                            <p class="text-xs text-stone-500 mt-3 leading-relaxed">
                                <i class="fa-solid fa-circle-info text-laranja-600 mr-1"></i> Clique em <strong>"Ver no Mapa"</strong> em qualquer card ao lado para centralizar o mapa no ponto selecionado.
                            </p>
                        </div>

                        <!-- Card Complementar para Equilibrar a Coluna do Mapa -->
                        <div class="bg-oliva-900 text-white p-6 rounded-3xl shadow-md space-y-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-xl bg-laranja-600 text-white flex items-center justify-center">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                </span>
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase text-laranja-400 block">Consumo Consciente</span>
                                    <h4 class="font-display font-bold text-base">Checklist para Ir à Feira em Taubaté</h4>
                                </div>
                            </div>
                            <ul class="space-y-2 text-xs text-stone-200 leading-relaxed">
                                <li><i class="fa-solid fa-check text-laranja-400 mr-1.5"></i> Leve <strong>ecobags e potes retornáveis</strong> para comprar grãos, queijos e temperos a granel no Mercatau.</li>
                                <li><i class="fa-solid fa-check text-laranja-400 mr-1.5"></i> Peça as <strong>ramas de cenoura e folhas de beterraba</strong> que costumam ficar nas bancas — são gratuitas e super nutritivas!</li>
                                <li><i class="fa-solid fa-check text-laranja-400 mr-1.5"></i> Prefira frutas e verduras da época: são mais baratas, doces e exigem menos defensivos.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <!-- =================================================================== -->
    <!-- SEÇÃO: GRANDE PROJETO MUNDIAL DE ALIMENTAÇÃO SUSTENTÁVEL            -->
    <!-- =================================================================== -->
    <section id="projeto-mundial" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-flex items-center gap-1.5 bg-laranja-100 text-laranja-700 text-xs font-extrabold uppercase tracking-widest px-4 py-1.5 rounded-full mb-3">
                <i class="fa-solid fa-earth-americas"></i> Referência Mundial em Alimentação Sustentável
            </span>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-grafite">
                <?= htmlspecialchars($DADOS_PROJETO_MUNDIAL['nome']) ?>
            </h2>
            <p class="text-stone-600 mt-3 text-base leading-relaxed">
                <?= htmlspecialchars($DADOS_PROJETO_MUNDIAL['subtitulo']) ?>
            </p>
        </div>

        <div class="bg-oliva-900 text-white rounded-3xl p-7 sm:p-10 lg:p-12 shadow-2xl mb-12">
            <div class="grid lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-6 space-y-5">
                    <div class="inline-flex items-center gap-2 bg-laranja-600 text-white text-xs font-extrabold uppercase tracking-wider px-3.5 py-1.5 rounded-full">
                        <i class="fa-solid fa-award"></i> História e Funcionamento do Projeto
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="bg-white/10 border border-white/15 p-4 rounded-2xl">
                            <span class="text-[11px] font-extrabold uppercase text-laranja-400 block mb-1">
                                <i class="fa-solid fa-user-pen mr-1"></i> Quem Criou (Fundador)
                            </span>
                            <p class="text-sm font-bold text-white"><?= htmlspecialchars($DADOS_PROJETO_MUNDIAL['criador']) ?></p>
                        </div>

                        <div class="bg-white/10 border border-white/15 p-4 rounded-2xl">
                            <span class="text-[11px] font-extrabold uppercase text-laranja-400 block mb-1">
                                <i class="fa-solid fa-calendar-check mr-1"></i> Quando Surgiu
                            </span>
                            <p class="text-sm font-bold text-white"><?= htmlspecialchars($DADOS_PROJETO_MUNDIAL['quando']) ?></p>
                        </div>
                    </div>

                    <div class="bg-white/10 border border-white/15 p-4 rounded-2xl">
                        <span class="text-[11px] font-extrabold uppercase text-laranja-400 block mb-1">
                            <i class="fa-solid fa-location-dot mr-1"></i> Onde Surgiu & Alcance Atual
                        </span>
                        <p class="text-sm font-bold text-white"><?= htmlspecialchars($DADOS_PROJETO_MUNDIAL['onde']) ?></p>
                    </div>

                    <div class="bg-black/25 border border-white/10 p-5 rounded-2xl space-y-2">
                        <span class="text-xs font-extrabold uppercase text-laranja-400 block">
                            <i class="fa-solid fa-gears mr-1"></i> Como o Projeto Funciona na Prática?
                        </span>
                        <p class="text-xs sm:text-sm text-stone-200 leading-relaxed">
                            <?= htmlspecialchars($DADOS_PROJETO_MUNDIAL['como_funciona']) ?>
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-3 pt-1">
                        <?php foreach ($DADOS_PROJETO_MUNDIAL['estatisticas'] as $est): ?>
                        <div class="bg-white/5 border border-white/10 p-3.5 rounded-2xl text-center">
                            <span class="font-display font-extrabold text-lg sm:text-xl text-laranja-400 block"><?= htmlspecialchars($est['numero']) ?></span>
                            <p class="text-[11px] text-stone-300 mt-1 leading-snug"><?= htmlspecialchars($est['rotulo']) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="lg:col-span-6 space-y-6">
                    <div class="bg-black/40 p-4 rounded-3xl border border-white/15 shadow-lg">
                        <div class="flex items-center justify-between mb-3 px-1">
                            <span class="text-xs font-extrabold uppercase text-laranja-400 flex items-center gap-1.5">
                                <i class="fa-brands fa-youtube text-red-500 text-base"></i> <?= htmlspecialchars($DADOS_PROJETO_MUNDIAL['video_titulo']) ?>
                            </span>
                            <span class="text-[10px] font-bold bg-white/10 px-2.5 py-0.5 rounded-full text-stone-200">PT-BR</span>
                        </div>
                        <div class="aspect-video w-full rounded-2xl overflow-hidden bg-black border border-white/10">
                            <iframe src="<?= htmlspecialchars($DADOS_PROJETO_MUNDIAL['video_embed']) ?>"
                                    class="w-full h-full border-0"
                                    title="<?= htmlspecialchars($DADOS_PROJETO_MUNDIAL['video_titulo']) ?>"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    loading="lazy"></iframe>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-3">
                        <?php foreach ($DADOS_PROJETO_MUNDIAL['pilares'] as $pilar): ?>
                        <div class="bg-white text-grafite p-4 rounded-2xl shadow">
                            <div class="w-8 h-8 rounded-xl bg-laranja-100 text-laranja-600 flex items-center justify-center mb-2 text-sm">
                                <i class="fa-solid <?= htmlspecialchars($pilar['icone']) ?>"></i>
                            </div>
                            <h4 class="font-display font-bold text-sm text-oliva-900 mb-1"><?= htmlspecialchars($pilar['titulo']) ?></h4>
                            <p class="text-[11px] text-stone-600 leading-relaxed"><?= htmlspecialchars($pilar['texto']) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <div>
            <h3 class="font-display text-2xl font-extrabold text-grafite mb-6 text-center">
                Iniciativas no Brasil que Seguem Essa Mesma Missão
            </h3>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card-sustenta rounded-3xl overflow-hidden">
                    <div class="h-40 w-full img-zoom-container">
                        <img src="https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=600&q=80" alt="Programa Mesa Brasil SESC" class="w-full h-full object-cover" loading="lazy">
                    </div>
                    <div class="p-6">
                        <span class="text-[11px] font-bold uppercase text-oliva-700">Rede Nacional</span>
                        <h4 class="font-display font-bold text-lg text-grafite mt-1 mb-2">Programa Mesa Brasil SESC</h4>
                        <p class="text-xs text-stone-600 leading-relaxed">
                            Atuante também em Taubaté, recolhe alimentos seguros que perderam valor comercial e os distribui a instituições sociais, unindo logística solidária e educação nutricional.
                        </p>
                    </div>
                </div>

                <div class="card-sustenta rounded-3xl overflow-hidden">
                    <div class="h-40 w-full img-zoom-container">
                        <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=600&q=80" alt="Bancos de Alimentos Municipais" class="w-full h-full object-cover" loading="lazy">
                    </div>
                    <div class="p-6">
                        <span class="text-[11px] font-bold uppercase text-oliva-700">Política Pública</span>
                        <h4 class="font-display font-bold text-lg text-grafite mt-1 mb-2">Bancos de Alimentos</h4>
                        <p class="text-xs text-stone-600 leading-relaxed">
                            Equipamentos públicos que captam excedentes de feiras livres, centrais de abastecimento e supermercados para processamento mínimo e doação a famílias em vulnerabilidade.
                        </p>
                    </div>
                </div>

                <div class="card-sustenta rounded-3xl overflow-hidden">
                    <div class="h-40 w-full img-zoom-container">
                        <img src="https://images.unsplash.com/photo-1573246123716-6b1782bfc499?auto=format&fit=crop&w=600&q=80" alt="Instituto Feira Livre e CSAs" class="w-full h-full object-cover" loading="lazy">
                    </div>
                    <div class="p-6">
                        <span class="text-[11px] font-bold uppercase text-oliva-700">Comércio Justo</span>
                        <h4 class="font-display font-bold text-lg text-grafite mt-1 mb-2">Comunidades CSA Brasil</h4>
                        <p class="text-xs text-stone-600 leading-relaxed">
                            Comunidades que Sustentam a Agricultura eliminam atravessadores e conectam famílias urbanas diretamente ao financiamento mensal de pequenos produtores orgânicos.
                        </p>
                    </div>
                </div>

                <div class="card-sustenta rounded-3xl overflow-hidden">
    <div class="h-40 w-full img-zoom-container">
        <img src="img/favela.jpg" alt="Favela Orgânica e Gastronomia Sustentável" class="w-full h-full object-cover" loading="lazy">
    </div>
    <div class="p-6">
        <span class="text-[11px] font-bold uppercase text-oliva-700">Gastronomia Social</span>
        <h4 class="font-display font-bold text-lg text-grafite mt-1 mb-2">Favela Orgânica (Regina Tchelly)</h4>                        <p class="text-xs text-stone-600 leading-relaxed">
                            Pioneira no Brasil e parceira do movimento Slow Food na popularização da culinária de cascas e talos, compostagem comunitária e valorização da comida de verdade.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================== -->
    <!-- FONTES E REFERÊNCIAS BIBLIOGRÁFICAS                                 -->
    <!-- =================================================================== -->
    <section id="referencias" class="py-16 bg-white border-t border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-11 h-11 rounded-2xl bg-oliva-700 text-white flex items-center justify-center text-lg">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-wider text-laranja-600">Base Científica e Documental</span>
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-grafite">Fontes e Referências Bibliográficas</h2>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 text-xs text-stone-700 leading-relaxed">
                <div class="p-4 rounded-2xl bg-creme border border-stone-200">
                    <strong>1. SLOW FOOD INTERNACIONAL & BRASIL.</strong> <em>Movimento Global pelo Alimento Bom, Limpo e Justo & Catálogo Arca do Gosto.</em> Disponível em: <a href="https://www.slowfood.com/" target="_blank" rel="noopener" class="text-laranja-600 font-semibold hover:underline">https://www.slowfood.com/</a> | <a href="https://slowfoodbrasil.org/" target="_blank" rel="noopener" class="text-laranja-600 font-semibold hover:underline">https://slowfoodbrasil.org/</a>
                </div>
                <div class="p-4 rounded-2xl bg-creme border border-stone-200">
                    <strong>2. PENSAMENTO VERDE & ECONSCIENTE.</strong> <em>Receitas sustentáveis para evitar o desperdício de alimentos na cozinha.</em> Disponível em: <a href="https://www.pensamentoverde.com.br/dicas/receitas-sustentaveis-nao-desperdice-ingredientes-cozinha/" target="_blank" rel="noopener" class="text-laranja-600 font-semibold hover:underline">pensamentoverde.com.br</a> | <a href="https://econsciente.com.br/receitas-sustentaveis-evitar-desperdicio/" target="_blank" rel="noopener" class="text-laranja-600 font-semibold hover:underline">econsciente.com.br</a>
                </div>
                <div class="p-4 rounded-2xl bg-creme border border-stone-200">
                    <strong>3. RECEITERIA.</strong> <em>Receitas para reaproveitar as sobras de alimentos com sabor e economia.</em> Disponível em: <a href="https://www.receiteria.com.br/receitas-para-reaproveitar-as-sobras/" target="_blank" rel="noopener" class="text-laranja-600 font-semibold hover:underline">https://www.receiteria.com.br/receitas-para-reaproveitar-as-sobras/</a>
                </div>
                <div class="p-4 rounded-2xl bg-creme border border-stone-200">
                    <strong>4. FAO / ONU (Organização das Nações Unidas para a Alimentação e a Agricultura).</strong> <em>Food Wastage Footprint: Impacts on Natural Resources & Relatório SOFI.</em> Disponível em: <a href="https://www.fao.org/brasil/pt/" target="_blank" rel="noopener" class="text-laranja-600 font-semibold hover:underline">https://www.fao.org/brasil/pt/</a>
                </div>
                <div class="p-4 rounded-2xl bg-creme border border-stone-200">
                    <strong>5. EMBRAPA (Empresa Brasileira de Pesquisa Agropecuária).</strong> <em>Perdas e Desperdício de Alimentos no Brasil e Agroecologia.</em> Brasília, DF. Disponível em: <a href="https://www.embrapa.br/tema-perdas-e-desperdicio-de-alimentos" target="_blank" rel="noopener" class="text-laranja-600 font-semibold hover:underline">https://www.embrapa.br/tema-perdas-e-desperdicio-de-alimentos</a>
                </div>
                <div class="p-4 rounded-2xl bg-creme border border-stone-200">
                    <strong>6. IBGE (Instituto Brasileiro de Geografia e Estatística).</strong> <em>Censo Agropecuário: Agricultura Familiar no Brasil e Vale do Paraíba.</em> Disponível em: <a href="https://www.ibge.gov.br/" target="_blank" rel="noopener" class="text-laranja-600 font-semibold hover:underline">https://www.ibge.gov.br/</a>
                </div>
                <div class="p-4 rounded-2xl bg-creme border border-stone-200">
                    <strong>7. WATER FOOTPRINT NETWORK (MEKONNEN, M. M.; HOEKSTRA, A. Y.).</strong> <em>The Green, Blue and Grey Water Footprint of Crops and Farm Animal Products.</em> Disponível em: <a href="https://www.waterfootprint.org/" target="_blank" rel="noopener" class="text-laranja-600 font-semibold hover:underline">https://www.waterfootprint.org/</a>
                </div>
                <div class="p-4 rounded-2xl bg-creme border border-stone-200">
                    <strong>8. PREFEITURA MUNICIPAL DE TAUBATÉ, SESC MESA BRASIL & INSTITUTO AKATU.</strong> <em>Agricultura Familiar, Feiras Livres de Taubaté e Consumo Consciente.</em> Disponível em: <a href="https://taubate.sp.gov.br/" target="_blank" rel="noopener" class="text-laranja-600 font-semibold hover:underline">https://taubate.sp.gov.br/</a> | <a href="https://akatu.org.br/" target="_blank" rel="noopener" class="text-laranja-600 font-semibold hover:underline">https://akatu.org.br/</a>
                </div>
            </div>
        </div>
    </section>

    <!-- =================================================================== -->
    <!-- RODAPÉ INSTITUCIONAL                                                -->
    <!-- =================================================================== -->
    <footer class="bg-oliva-900 text-stone-300 py-12 border-t border-oliva-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-laranja-600 flex items-center justify-center text-white">
                    <i class="fa-solid fa-seedling"></i>
                </div>
                <div>
                    <span class="font-display font-bold text-white text-lg">SUSTENTA+ • Equipe 03</span>
                    <p class="text-xs text-stone-400">Alimentação Sustentável: Do campo ao prato: produzir, consumir e desperdiçar</p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <a href="#topo" class="hover:text-laranja-400 transition-colors"><i class="fa-solid fa-arrow-up mr-1"></i> Voltar ao Topo</a>
                <span>•</span>
                <a href="admin.php" class="text-laranja-400 font-bold hover:underline"><i class="fa-solid fa-user-shield mr-1"></i> Painel ADM</a>
            </div>
        </div>
    </footer>

    <!-- =================================================================== -->
    <!-- MODAIS INTERATIVOS (DETALHE RECEITA, NOVA RECEITA E LOCAL)          -->
    <!-- =================================================================== -->
    <!-- Modal 1: Receita + Comentários -->
    <div id="modal-receita-detalhe" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl relative animate-fade-in">
            <button type="button" onclick="fecharModal('modal-receita-detalhe')" class="absolute top-5 right-5 z-10 w-10 h-10 rounded-full bg-white/90 hover:bg-red-100 text-stone-700 hover:text-red-600 flex items-center justify-center shadow">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <div id="conteudo-modal-receita"></div>

            <div class="mt-8 pt-6 border-t border-stone-200">
                <h4 class="font-display font-bold text-lg text-grafite mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-comments text-laranja-600"></i> Avaliações dos Visitantes
                </h4>
                <div id="lista-comentarios-modal-receita" class="space-y-3 mb-6"></div>

                <form method="POST" action="index.php" class="bg-creme p-5 rounded-2xl border border-stone-200 space-y-4">
                    <input type="hidden" name="acao" value="novo_comentario">
                    <input type="hidden" name="tipo_origem" value="receita">
                    <input type="hidden" name="item_id" id="input-comentario-receita-id" value="">

                    <h5 class="font-display font-bold text-sm text-oliva-800">
                        <i class="fa-solid fa-star text-amber-500 mr-1"></i> Deixe sua Avaliação sobre esta Receita
                    </h5>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-stone-600 mb-1">Seu Nome *</label>
                            <input type="text" name="nome_usuario" required placeholder="Digite seu nome" class="w-full p-2.5 rounded-xl border border-stone-300 text-sm bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-600 mb-1">Nota (1 a 5 Estrelas) *</label>
                            <select name="nota" class="w-full p-2.5 rounded-xl border border-stone-300 text-sm bg-white font-semibold text-amber-600">
                                <option value="5">⭐⭐⭐⭐⭐ (5 - Excelente)</option>
                                <option value="4">⭐⭐⭐⭐ (4 - Muito Boa)</option>
                                <option value="3">⭐⭐⭐ (3 - Boa)</option>
                                <option value="2">⭐⭐ (2 - Regular)</option>
                                <option value="1">⭐ (1 - Fraca)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-600 mb-1">Seu Comentário *</label>
                        <textarea name="texto" rows="2" required placeholder="O que achou desta receita sustentável?" class="w-full p-2.5 rounded-xl border border-stone-300 text-sm bg-white"></textarea>
                    </div>

                    <button type="submit" class="bg-laranja-600 hover:bg-laranja-700 text-white text-xs font-bold px-5 py-3 rounded-xl transition-colors">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Enviar Avaliação
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 2: Enviar Nova Receita -->
    <div id="modal-nova-receita" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl relative animate-fade-in">
            <button type="button" onclick="fecharModal('modal-nova-receita')" class="absolute top-5 right-5 w-10 h-10 rounded-full bg-stone-100 hover:bg-red-100 text-stone-600 hover:text-red-600 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <h3 class="font-display font-bold text-2xl text-grafite mb-4">Compartilhar Receita Sustentável</h3>

            <form method="POST" action="index.php" class="space-y-4">
                <input type="hidden" name="acao" value="nova_receita">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Título da Receita *</label>
                        <input type="text" name="titulo" required placeholder="Ex: Farofa Crocante de Talos e Cascas" class="w-full p-3 rounded-xl border border-stone-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Categoria *</label>
                        <select name="categoria" class="w-full p-3 rounded-xl border border-stone-300 text-sm bg-white font-semibold">
                            <option value="Aproveitamento Integral">Aproveitamento Integral</option>
                            <option value="Vegana/Vegetariana">Vegana/Vegetariana</option>
                            <option value="Rápida & Econômica">Rápida & Econômica</option>
                            <option value="Tradicional Regional">Tradicional Regional</option>
                        </select>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Seu Nome / Fonte *</label>
                        <input type="text" name="autor" required placeholder="Seu nome ou turma" class="w-full p-3 rounded-xl border border-stone-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">URL de Foto do Prato (Opcional)</label>
                        <input type="url" name="imagem_url" placeholder="https://..." class="w-full p-3 rounded-xl border border-stone-300 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Ingredientes (separados por vírgula) *</label>
                    <textarea name="ingredientes" rows="2" required placeholder="Ex: Talos de couve, Farinha de mandioca, Cebola, Cenoura ralada com casca..." class="w-full p-3 rounded-xl border border-stone-300 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Modo de Preparo *</label>
                    <textarea name="modo_preparo" rows="3" required placeholder="Explique o passo a passo da receita..." class="w-full p-3 rounded-xl border border-stone-300 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Dica Sustentável *</label>
                    <input type="text" name="dica_sustentavel" required placeholder="Como essa receita ajuda a evitar o desperdício?" class="w-full p-3 rounded-xl border border-stone-300 text-sm">
                </div>
                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" onclick="fecharModal('modal-nova-receita')" class="px-5 py-2.5 rounded-xl border border-stone-300 text-xs font-bold text-stone-600">Cancelar</button>
                    <button type="submit" class="bg-laranja-600 hover:bg-laranja-700 text-white text-xs font-bold px-6 py-2.5 rounded-xl shadow">Publicar Receita</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Comentários dos Locais Sustentáveis -->
    <div id="modal-local-comentarios" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl relative animate-fade-in">
            <button type="button" onclick="fecharModal('modal-local-comentarios')" class="absolute top-5 right-5 w-10 h-10 rounded-full bg-stone-100 hover:bg-red-100 text-stone-600 hover:text-red-600 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <div id="cabecalho-modal-local" class="mb-5"></div>
            <h4 class="font-display font-bold text-sm uppercase tracking-wider text-stone-500 mb-3">Avaliações dos Visitantes:</h4>
            <div id="lista-comentarios-modal-local" class="space-y-3 mb-6"></div>

            <form method="POST" action="index.php" class="bg-creme p-5 rounded-2xl border border-stone-200 space-y-4">
                <input type="hidden" name="acao" value="novo_comentario">
                <input type="hidden" name="tipo_origem" value="local">
                <input type="hidden" name="item_id" id="input-comentario-local-id" value="">

                <h5 class="font-display font-bold text-sm text-oliva-800">Enviar Avaliação deste Local</h5>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-600 mb-1">Seu Nome *</label>
                        <input type="text" name="nome_usuario" required placeholder="Seu nome" class="w-full p-2.5 rounded-xl border border-stone-300 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-600 mb-1">Nota (1 a 5) *</label>
                        <select name="nota" class="w-full p-2.5 rounded-xl border border-stone-300 text-sm bg-white font-semibold text-amber-600">
                            <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                            <option value="4">⭐⭐⭐⭐ (4)</option>
                            <option value="3">⭐⭐⭐ (3)</option>
                            <option value="2">⭐⭐ (2)</option>
                            <option value="1">⭐ (1)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-600 mb-1">Comentário *</label>
                    <textarea name="texto" rows="2" required placeholder="Compartilhe sua experiência ou dica sobre este local..." class="w-full p-2.5 rounded-xl border border-stone-300 text-sm bg-white"></textarea>
                </div>
                <button type="submit" class="bg-oliva-700 hover:bg-oliva-800 text-white text-xs font-bold px-5 py-3 rounded-xl">Enviar Avaliação</button>
            </form>
        </div>
    </div>

    <!-- Ponte de Dados PHP (dados.php + MySQL) -> JavaScript -->
    <script>
        window.SUSTENTA_DATA = {
            receitas: <?= json_encode($receitas_db, JSON_UNESCAPED_UNICODE) ?>,
            locais:   <?= json_encode($locais_db, JSON_UNESCAPED_UNICODE) ?>,
            mitos:    <?= json_encode($DADOS_MITOS, JSON_UNESCAPED_UNICODE) ?>,
            plantio:  <?= json_encode($DADOS_PLANTIO, JSON_UNESCAPED_UNICODE) ?>,
            plantio_mysql: <?= json_encode($plantio_db, JSON_UNESCAPED_UNICODE) ?>
        };
    </script>
    <script src="script.js"></script>
</body>
</html>