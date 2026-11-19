<?php
/**
 * PROJETO ACADÊMICO: SUSTENTA+ — Tecnologia, Informação e Sustentabilidade
 * EQUIPE 03: Alimentação Sustentável — Do campo ao prato: produzir, consumir e desperdiçar
 * ARQUIVO: dados.php (Dados Cientificamente Verificados, Locais Reais de Taubaté e Imagens Reais Selecionadas)
 */

// ===================================================================
// 1. DESTAQUE MUNDIAL: O MAIOR MOVIMENTO GLOBAL DO CAMPO AO PRATO
// ===================================================================
$DADOS_PROJETO_MUNDIAL = [
    'nome'          => 'Movimento Slow Food Internacional & Arca do Gosto',
    'subtitulo'     => 'A maior organização mundial dedicada à alimentação "Boa, Limpa e Justa" — conectando quem produz no campo a quem consome na cidade.',
    'criador'       => 'Carlo Petrini (sociólogo, jornalista e ativista italiano)',
    'quando'        => 'Fundado em 1986 na Itália (Manifesto Internacional assinado em 1989, em Paris)',
    'onde'          => 'Nasceu na cidade de Bra (Piemonte) e em Roma, na Itália • Hoje atua em mais de 160 países (incluindo todo o Brasil)',
    'como_funciona' => 'Surgiu como uma resposta global à padronização do fast-food industrial e ao desperdício na cadeia alimentar. O movimento organiza redes locais (chamadas de Convivia e Comunidades) que unem pequenos agricultores familiares, feirantes, chefs de cozinha, escolas e consumidores para proteger alimentos tradicionais, eliminar intermediários abusivos e combater o desperdício do campo à mesa.',
    'video_titulo'  => 'Vídeo em Português: A Jornada do Alimento e o Combate ao Desperdício (Do Campo à Mesa)',
    'video_embed'   => 'https://www.youtube.com/embed/ishA6kry8nc',
    'estatisticas'  => [
        ['numero' => '+160 Países', 'rotulo' => 'Presença ativa em todos os continentes, incluindo dezenas de núcleos no Brasil.'],
        ['numero' => '+6.000 Alimentos', 'rotulo' => 'Salvos da extinção pelo catálogo mundial "Arca do Gosto" (sementes, frutas e queijos).'],
        ['numero' => '+1 Milhão', 'rotulo' => 'De ativistas, agricultores familiares, estudantes e cozinheiros conectados na rede.']
    ],
    'pilares' => [
        [
            'titulo' => '1. Alimento BOM',
            'icone'  => 'fa-bowl-food',
            'texto'  => 'Fresco, sazonal, nutritivo e preparado respeitando a cultura e a identidade regional de cada território.'
        ],
        [
            'titulo' => '2. Alimento LIMPO',
            'icone'  => 'fa-seedling',
            'texto'  => 'Produzido e transportado sem agredir os solos, os rios e a biodiversidade, promovendo agroecologia e zero desperdício.'
        ],
        [
            'titulo' => '3. Alimento JUSTO',
            'icone'  => 'fa-scale-balanced',
            'texto'  => 'Preços acessíveis para os consumidores urbanos e remuneração digna e justa para quem trabalha na terra.'
        ]
    ],
    'galeria' => []
];

// ===================================================================
// 2. INGREDIENTES DA GELADEIRA INTELIGENTE (16 ITENS CONECTADOS)
// ===================================================================
$DADOS_GELADEIRA = [
    ['nome' => 'Arroz cozido', 'icone' => 'fa-bowl-rice'],
    ['nome' => 'Feijão cozido', 'icone' => 'fa-jar'],
    ['nome' => 'Pão amanhecido', 'icone' => 'fa-bread-slice'],
    ['nome' => 'Casca de Banana', 'icone' => 'fa-leaf'],
    ['nome' => 'Casca de Manga', 'icone' => 'fa-lemon'],
    ['nome' => 'Casca de Abacaxi', 'icone' => 'fa-seedling'],
    ['nome' => 'Casca de Melancia', 'icone' => 'fa-apple-whole'],
    ['nome' => 'Casca de Laranja/Mamão', 'icone' => 'fa-spa'],
    ['nome' => 'Talos de Brócolis/Couve', 'icone' => 'fa-tree'],
    ['nome' => 'Talos de Beterraba/Espinafre', 'icone' => 'fa-carrot'],
    ['nome' => 'Cenoura', 'icone' => 'fa-carrot'],
    ['nome' => 'Batata', 'icone' => 'fa-circle'],
    ['nome' => 'Frango ou Carne de sobra', 'icone' => 'fa-drumstick-bite'],
    ['nome' => 'Tomate', 'icone' => 'fa-pepper-hot'],
    ['nome' => 'Ovos', 'icone' => 'fa-egg'],
    ['nome' => 'Leite', 'icone' => 'fa-mug-hot']
];

// ===================================================================
// 3. BANCO DE 30 PERGUNTAS DE MITO OU VERDADE (SORTEIA 10 A CADA RESET)
// ===================================================================
$DADOS_MITOS = [
    [
        'id' => 1,
        'pergunta' => "Alimentos 'feios' ou tortos têm menos nutrientes e vitaminas que os alimentos bonitos?",
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! O formato irregular de cenouras, tomates ou batatas deve-se apenas a variações naturais de solo e clima. O valor nutricional é 100% idêntico.'
    ],
    [
        'id' => 2,
        'pergunta' => 'Plástico biodegradável se decompõe rápido em qualquer lugar da natureza?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! A maioria dos bioplásticos exige usinas industriais de compostagem acima de 55°C. Em aterros comuns ou oceanos, podem levar décadas para degradar.'
    ],
    [
        'id' => 3,
        'pergunta' => 'Congelar sobras de refeições estraga todas as vitaminas da comida?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! O congelamento preserva proteínas, fibras, minerais e a maioria das vitaminas, pois apenas paralisa a ação de microrganismos.'
    ],
    [
        'id' => 4,
        'pergunta' => 'Comer produtos da estação (safra) reduz o uso de agrotóxicos e a pegada hídrica?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! Na época natural de safra, a planta desenvolve-se sob o clima ideal da região, sofrendo menos pragas e exigindo menos irrigação artificial.'
    ],
    [
        'id' => 5,
        'pergunta' => 'Cascas, talos e folhas (como rama de cenoura e casca de banana) têm alto valor nutricional?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! Segundo a Embrapa e a Tabela TACO/Unicamp, folhas de cenoura têm mais cálcio e vitamina C que a raiz, e a casca de banana tem mais que o dobro de fibras da polpa.'
    ],
    [
        'id' => 6,
        'pergunta' => 'Restos de comida misturados no saco de lixo comum viram adubo rapidamente no aterro sanitário?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! Soterrados sem oxigênio junto com plásticos no aterro, os resíduos orgânicos sofrem decomposição anaeróbica, gerando chorume e gás metano (CH4).'
    ],
    [
        'id' => 7,
        'pergunta' => 'A agricultura familiar representa 77% dos estabelecimentos agrícolas do Brasil e lidera na produção de mandioca e feijão?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! Segundo o Censo Agropecuário do IBGE, 77% das propriedades rurais do país são familiares, respondendo por 87% da mandioca, 70% do feijão e grande parte das hortaliças.'
    ],
    [
        'id' => 8,
        'pergunta' => 'Lavar e guardar folhas verdes ainda molhadas dentro da geladeira faz elas durarem mais tempo?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! O excesso de umidade acelera o apodrecimento. O ideal é higienizar, secar muito bem e guardar em pote fechado com papel-toalha seco.'
    ],
    [
        'id' => 9,
        'pergunta' => 'A entrecasca branca da melancia e a casca da manga podem ser consumidas cozidas, assadas ou em sucos?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! A parte branca da melancia representa quase 45% do peso da fruta e rende ótimos refogados e doces, enquanto a casca de manga é rica em fibras e pectina.'
    ],
    [
        'id' => 10,
        'pergunta' => 'Reduzir o desperdício de alimentos ajuda diretamente a economizar água dos rios e reservatórios?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! Cada quilo de alimento jogado fora desperdiça toda a "água virtual" usada na sua irrigação — como 2.497 litros de água por quilo de arroz.'
    ],
    [
        'id' => 11,
        'pergunta' => 'Produzir 1 kg de carne bovina consome a mesma quantidade de água que produzir 1 kg de tomate?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! Segundo a Water Footprint Network, 1 kg de carne bovina exige cerca de 15.415 litros de água, enquanto 1 kg de tomate requer aproximadamente 214 litros.'
    ],
    [
        'id' => 12,
        'pergunta' => 'No Brasil, a maior parte das perdas de alimentos acontece durante o manuseio, transporte e abastecimento?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! Dados da Embrapa apontam que 50% das perdas ocorrem no manuseio e transporte rodoviário e 30% nas centrais de abastecimento (CEASAs).'
    ],
    [
        'id' => 13,
        'pergunta' => 'Composteiras domésticas bem manejadas com matéria seca dão mau cheiro e atraem ratos?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! Quando se intercala 1 parte de resíduo vegetal cru com 2 partes de serragem ou folhas secas (sem colocar carnes ou gorduras), a compostagem tem cheiro de terra de floresta.'
    ],
    [
        'id' => 14,
        'pergunta' => 'O gás metano (CH4) emitido pela pecuária e por aterros retém mais calor na atmosfera que o CO2?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! Segundo o IPCC/ONU, o potencial de aquecimento global do metano em um horizonte de 100 anos é cerca de 28 vezes superior ao do dióxido de carbono (CO2).'
    ],
    [
        'id' => 15,
        'pergunta' => 'Sementes de abóbora devem ser sempre descartadas porque não possuem nutrientes digeríveis?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! Lavadas e tostadas no forno com uma pitada de sal, as sementes de abóbora são excelentes fontes de zinco, magnésio, fibras e gorduras boas.'
    ],
    [
        'id' => 16,
        'pergunta' => 'Comprar diretamente em feiras de produtores locais em Taubaté reduz as emissões de carbono do transporte (Food Miles)?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! Circuitos curtos de comercialização evitam que os alimentos viagem centenas de quilômetros em caminhões movidos a óleo diesel.'
    ],
    [
        'id' => 17,
        'pergunta' => 'Embalagens de isopor (EPS) e filmes plásticos esticáveis são facilmente reciclados em qualquer coleta seletiva?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! Bandejas de isopor e filmes plásticos sujos com líquidos de alimentos têm baixíssima viabilidade econômica de reciclagem e acabam nos aterros.'
    ],
    [
        'id' => 18,
        'pergunta' => 'As PANCs (Plantas Alimentícias Não Convencionais), como ora-pro-nóbis e taioba, exigem muito agrotóxico para crescer?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! Por serem plantas rústicas e adaptadas ao nosso bioma (Mata Atlântica), as PANCs são naturalmente resistentes a pragas e crescem sem veneno.'
    ],
    [
        'id' => 19,
        'pergunta' => 'A folha de taioba crua pode causar irritação na garganta e deve ser consumida sempre refogada ou cozida?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! A taioba possui cristais de oxalato de cálcio que são totalmente neutralizados pelo calor do cozimento, tornando-a deliciosa e segura quando refogada.'
    ],
    [
        'id' => 20,
        'pergunta' => 'O Brasil possui uma lei federal que exige que pelo menos 30% da merenda escolar pública venha da agricultura familiar?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! A Lei nº 11.947/2009 do PNAE determina que no mínimo 30% dos recursos federais da alimentação escolar sejam usados na compra direta da agricultura familiar.'
    ],
    [
        'id' => 21,
        'pergunta' => 'Guardar tomates maduros junto com bananas na fruteira faz as bananas amadurecerem mais devagar?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! Bananas e tomates maduros liberam gás etileno natural, que acelera o amadurecimento (e o escurecimento) das frutas próximas.'
    ],
    [
        'id' => 22,
        'pergunta' => 'O arroz cultivado na várzea do Rio Paraíba do Sul (Taubaté, Tremembé e Pinda) é uma tradição agrícola centenária?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! A rizicultura nas várzeas do Paraíba do Sul tem mais de 100 anos de história e hoje destaca-se também pelos arrozes especiais e transição agroecológica.'
    ],
    [
        'id' => 23,
        'pergunta' => 'Alimentos ultraprocessados têm menor impacto ambiental porque duram mais tempo na prateleira?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! Segundo o Guia Alimentar do Ministério da Saúde, ultraprocessados baseiam-se em monoculturas extensivas, alto gasto energético industrial e múltiplas camadas de plástico.'
    ],
    [
        'id' => 24,
        'pergunta' => 'Colocar alimentos ainda pelando de quentes na geladeira aumenta o consumo de energia elétrica do aparelho?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! O vapor quente eleva a temperatura interna do refrigerador, forçando o compressor a trabalhar mais. Espere amornar antes de refrigerar.'
    ],
    [
        'id' => 25,
        'pergunta' => 'A borra de café e as cascas de ovos trituradas podem ser usadas como adubo rico em nitrogênio e cálcio para vasos?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! A casca de ovo seca e moída fornece cálcio essencial (ótimo para tomateiros) e a borra de café compostada fornece nitrogênio e matéria orgânica.'
    ],
    [
        'id' => 26,
        'pergunta' => 'Cerca de um terço (1/3) de todos os alimentos produzidos para consumo humano no mundo é perdido ou desperdiçado?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! Dados oficiais da FAO/ONU confirmam que aproximadamente 1,3 bilhão de toneladas de comida (um terço da produção global) perde-se anualmente.'
    ],
    [
        'id' => 27,
        'pergunta' => 'Sistemas Agroflorestais (SAFs) combinam árvores nativas com lavouras agrícolas no mesmo terreno, protegendo o solo?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! A agrofloresta imita a natureza: as árvores mantêm a umidade, evitam a erosão das chuvas e reciclam nutrientes para as hortaliças e frutíferas.'
    ],
    [
        'id' => 28,
        'pergunta' => 'A data de validade "Consumir de preferência antes de" significa que o alimento fica imediatamente tóxico no dia seguinte?',
        'respostaCorreta' => 'Mito',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'MITO! Segundo a FAO, muitos alimentos secos (como grãos, farinhas e massas) bem armazenados mantêm segurança após a data de qualidade mínima, devendo-se avaliar cheiro, cor e integridade.'
    ],
    [
        'id' => 29,
        'pergunta' => 'Descartar óleo de cozinha usado na pia contamina milhares de litros de água e encarece o tratamento de esgoto?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! Apenas 1 litro de óleo de fritura jogado no ralo pode poluir até 25 mil litros de água. O óleo usado deve ser guardado em garrafa PET para reciclagem em sabão ou biodiesel.'
    ],
    [
        'id' => 30,
        'pergunta' => 'Plantar feijão e leguminosas ajuda a enriquecer naturalmente a terra com nitrogênio sem precisar de adubo químico?',
        'respostaCorreta' => 'Verdade',
        'imagem' => 'img/mito.jpg',
        'explicacao' => 'VERDADE! As raízes do feijão, ervilha e grão-de-bico abrigam bactérias (rizóbios) que realizam a fixação biológica de nitrogênio do ar diretamente no solo.'
    ]
];

// ===================================================================
// 4. CATÁLOGO DE PLANTIO REGIONAL (18 CULTURAS - IMAGENS REAIS DE CADA PLANTA)
// ===================================================================
$DADOS_PLANTIO = [
    [
        'nome'         => 'Cebolinha, Salsinha, Coentro e Manjericão',
        'estacoes'     => ['Outono', 'Inverno', 'Primavera', 'Verao'],
        'espacos'      => ['Apartamento/Vasos pequenos', 'Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => '45 a 60 dias (rebrote contínuo)',
        'sol'          => '3 a 4 horas de sol direto por dia',
        'rega'         => 'Dia sim, dia não (manter úmido sem encharcar)',
        'imagem'       => 'img/plantio1.jpg',
        'dicaRegional' => 'Adaptam-se perfeitamente a vasos em janelas de apartamentos em Taubaté. Colha sempre as folhas externas para a planta continuar produzindo o ano todo.'
    ],
    [
        'nome'         => 'Alface Crespa, Rúcula e Espinafre',
        'estacoes'     => ['Outono', 'Inverno', 'Primavera'],
        'espacos'      => ['Apartamento/Vasos pequenos', 'Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => '35 a 50 dias (colheita rápida)',
        'sol'          => '4 horas de sol (tolera meia-sombra)',
        'rega'         => 'Diária no início da manhã',
        'imagem'       => 'img/plantio2.webp',
        'dicaRegional' => 'No Vale do Paraíba, cultive a meia-sombra nos dias mais quentes de primavera e sol pleno no outono/inverno. Os talos do espinafre rendem uma ótima omelete sustentável!'
    ],
    [
        'nome'         => 'Cenoura, Beterraba e Rabanete',
        'estacoes'     => ['Outono', 'Inverno', 'Primavera'],
        'espacos'      => ['Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => 'Rabanete: 28 dias | Cenoura/Beterraba: 75 a 90 dias',
        'sol'          => 'Sol pleno (mínimo de 5 horas diárias)',
        'rega'         => '1 vez ao dia mantendo o solo fofo e aerado',
        'imagem'       => 'img/plantio3.avif',
        'dicaRegional' => 'O rabanete é campeão para hortas escolares em Taubaté pelo ciclo rápido; as ramas da cenoura viram Pesto e os talos da beterraba viram Patê com ricota!'
    ],
    [
        'nome'         => 'Tomate-Cereja e Pimentão',
        'estacoes'     => ['Primavera', 'Verao', 'Outono'],
        'espacos'      => ['Apartamento/Vasos pequenos', 'Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => '70 a 90 dias após o plantio',
        'sol'          => 'Sol pleno (5 a 6 horas de sol direto)',
        'rega'         => '4 a 5 vezes por semana direto na terra (sem molhar folhas)',
        'imagem'       => 'img/plantio4.avif',
        'dicaRegional' => 'Excelente para vasos de 25cm em varandas. Misture casca de ovo seca e triturada ao composto caseiro para fornecer cálcio e evitar fundo preto nos frutos.'
    ],
    [
        'nome'         => 'Couve-Manteiga e Brócolis Ramoso',
        'estacoes'     => ['Outono', 'Inverno', 'Primavera'],
        'espacos'      => ['Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => '60 a 80 dias (colheita por vários meses)',
        'sol'          => 'Sol pleno (5 horas diárias)',
        'rega'         => '3 a 4 vezes por semana',
        'imagem'       => 'img/plantio5.jpg',
        'dicaRegional' => 'Base da culinária do Vale do Paraíba. Use 100% da planta: folhas no refogado e talos nas Panquecas Verdes e na Farofa Nutritiva.'
    ],
    [
        'nome'         => 'Quiabo, Abobrinha de Tronco e Feijão-Vagem',
        'estacoes'     => ['Primavera', 'Verao'],
        'espacos'      => ['Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => '55 a 75 dias',
        'sol'          => 'Sol pleno e temperaturas quentes',
        'rega'         => '3 a 4 vezes por semana (aproveita as chuvas de verão)',
        'imagem'       => 'img/plantio6.webp',
        'dicaRegional' => 'Desenvolvem-se com vigor na primavera e verão da Bacia do Rio Paraíba do Sul. O feijão-vagem fixa nitrogênio natural no solo!'
    ],
    [
        'nome'         => 'Alecrim, Orégano, Tomilho e Hortelã',
        'estacoes'     => ['Primavera', 'Verao', 'Outono', 'Inverno'],
        'espacos'      => ['Apartamento/Vasos pequenos', 'Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => '40 a 60 dias (perenes por vários anos)',
        'sol'          => '4 a 5 horas de sol diário (Hortelã aceita meia-sombra)',
        'rega'         => '2 a 3 vezes por semana (Alecrim prefere solo mais seco)',
        'imagem'       => 'img/plantio7.png',
        'dicaRegional' => 'Plante a hortelã sempre em vaso exclusivo pois suas raízes se espalham rápido. O alecrim fresco é perfeito para assar Chips de Casca de Batata!'
    ],
    [
        'nome'         => 'Morango em Vasos ou Jardineiras Suspensas',
        'estacoes'     => ['Outono', 'Inverno', 'Primavera'],
        'espacos'      => ['Apartamento/Vasos pequenos', 'Horta Escolar'],
        'colheita'     => '60 a 80 dias após o transplante das mudas',
        'sol'          => '4 a 6 horas de sol direto por dia',
        'rega'         => '3 a 4 vezes por semana sem molhar as flores',
        'imagem'       => 'img/plantio8.jpg',
        'dicaRegional' => 'Cultivar morango orgânico em vasos suspensos nas varandas de Taubaté evita lesmas e elimina agrotóxicos de uma das frutas mais pulverizadas no comércio.'
    ],
    [
        'nome'         => 'Ora-pro-nóbis, Taioba e Capuchinha (PANCs)',
        'estacoes'     => ['Primavera', 'Verao', 'Outono', 'Inverno'],
        'espacos'      => ['Apartamento/Vasos pequenos', 'Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => '50 a 70 dias (rebrote permanente o ano inteiro)',
        'sol'          => 'Sol pleno ou meia-sombra',
        'rega'         => '2 a 3 vezes por semana (muito resistentes à seca)',
        'imagem'       => 'img/plantio9.avif',
        'dicaRegional' => 'As PANCs são patrimônio da Mata Atlântica e da Serra da Mantiqueira: a Ora-pro-nóbis possui ~25% de proteína em matéria seca e pega facilmente por estaca.'
    ],
    [
        'nome'         => 'Mandioca (Macaxeira) e Batata-Doce',
        'estacoes'     => ['Primavera', 'Verao'],
        'espacos'      => ['Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => 'Batata-doce: 110 dias | Mandioca: 8 a 10 meses',
        'sol'          => 'Sol pleno (6 horas diárias)',
        'rega'         => 'Apenas no início do plantio (rústicas e resistentes)',
        'imagem'       => 'img/plantio10.jpg',
        'dicaRegional' => 'Culturas históricas dos bairros rurais de Taubaté (Registro e Monjolinho). As cascas da batata-doce assadas viram chips crocantes e nutritivos.'
    ],
    [
        'nome'         => 'Abóbora Cabotiá / Menina e Chuchu',
        'estacoes'     => ['Primavera', 'Verao'],
        'espacos'      => ['Quintal/Canteiro'],
        'colheita'     => '80 a 110 dias',
        'sol'          => 'Sol pleno (chuchu aceita cercas e caramanchões)',
        'rega'         => '3 vezes por semana na base da planta',
        'imagem'       => 'img/plantio11.jpg',
        'dicaRegional' => 'Da abóbora e do chuchu nada se perde: a casca cozinha junto, as sementes de abóbora torradas no forno viram petisco rico em zinco e os brotos vão na sopa!'
    ],
    [
        'nome'         => 'Milho-Verde Crioulo e Feijão-de-Vagem/Corda',
        'estacoes'     => ['Primavera', 'Verao'],
        'espacos'      => ['Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => 'Feijão: 65 dias | Milho-verde: 90 a 100 dias',
        'sol'          => 'Sol pleno o dia todo',
        'rega'         => '3 vezes por semana ou regime de chuvas de verão',
        'imagem'       => 'img/plantio12.webp',
        'dicaRegional' => 'Plante juntos no sistema tradicional "Milpa": o pé de milho serve de tutor natural para o feijão subir, e o feijão aduba o solo com nitrogênio para o milho.'
    ],
    [
        'nome'         => 'Alho-Poró, Cebola de Cabeça e Cebolinha Francesa',
        'estacoes'     => ['Outono', 'Inverno'],
        'espacos'      => ['Apartamento/Vasos pequenos', 'Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => '80 a 110 dias',
        'sol'          => 'Sol pleno (4 a 6 horas por dia)',
        'rega'         => 'Moderada (2 a 3 vezes por semana)',
        'imagem'       => 'img/plantio13.avif',
        'dicaRegional' => 'Dica zero desperdício: coloque a base branca (com raízes) do alho-poró ou da cebolinha comprada na feira em um copo com água por 5 dias e depois plante no vaso!'
    ],
    [
        'nome'         => 'Escarola (Chicória), Almeirão e Mostarda',
        'estacoes'     => ['Outono', 'Inverno', 'Primavera'],
        'espacos'      => ['Apartamento/Vasos pequenos', 'Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => '45 a 60 dias',
        'sol'          => '4 horas de sol diário',
        'rega'         => 'Diária ou dia sim, dia não',
        'imagem'       => 'img/plantio14.jpg',
        'dicaRegional' => 'Folhosas rústicas muito resistentes a pragas em hortas escolares de Taubaté. Refogadas com alho, recheiam tortas e pizzas de pão amanhecido.'
    ],
    [
        'nome'         => 'Pepino Caipira, Berinjela e Jiló',
        'estacoes'     => ['Primavera', 'Verao'],
        'espacos'      => ['Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => 'Pepino: 55 dias | Berinjela e Jiló: 85 a 100 dias',
        'sol'          => 'Sol pleno e calor (5 a 6 horas diárias)',
        'rega'         => '4 vezes por semana mantendo o solo úmido',
        'imagem'       => 'img/plantio15.jpg',
        'dicaRegional' => 'A casca roxa da berinjela concentra antocianinas antioxidantes e pode ser assada em tiras ou transformada em antepasto no forno.'
    ],
    [
        'nome'         => 'Gengibre e Cúrcuma (Açafrão-da-Terra)',
        'estacoes'     => ['Primavera', 'Verao'],
        'espacos'      => ['Apartamento/Vasos pequenos', 'Quintal/Canteiro'],
        'colheita'     => '6 a 8 meses (basta enterrar um pedaço com broto)',
        'sol'          => 'Meia-sombra ou sol filtrado (2 a 4 horas)',
        'rega'         => '3 vezes por semana no verão; suspender no inverno',
        'imagem'       => 'img/plantio16.webp',
        'dicaRegional' => 'Muito fáceis de plantar em vasos largos a partir de um rizoma da feira. A cúrcuma ralada na hora enriquece o Mexidão Nutritivo e a Farofa de Talos!'
    ],
    [
        'nome'         => 'Ervilha-Torta e Grão-de-Bico',
        'estacoes'     => ['Outono', 'Inverno'],
        'espacos'      => ['Apartamento/Vasos pequenos', 'Quintal/Canteiro', 'Horta Escolar'],
        'colheita'     => 'Ervilha-torta: 60 a 70 dias | Grão-de-bico: 100 dias',
        'sol'          => 'Sol pleno de inverno (5 horas diárias)',
        'rega'         => '2 a 3 vezes por semana (não toleram encharcamento)',
        'imagem'       => 'img/plantio17.webp',
        'dicaRegional' => 'A ervilha-torta é consumida inteira com a vagem crocante, zero desperdício! Ideal para ensinar fixação biológica de nitrogênio em hortas escolares.'
    ],
    [
        'nome'         => 'Mamoeiro, Bananeira e Maracujá de Quintal',
        'estacoes'     => ['Primavera', 'Verao', 'Outono'],
        'espacos'      => ['Quintal/Canteiro'],
        'colheita'     => 'Maracujá: 6 meses | Mamão e Banana: 9 a 12 meses',
        'sol'          => 'Sol pleno e solo rico em composto orgânico',
        'rega'         => '2 a 3 vezes por semana na fase jovem',
        'imagem'       => 'img/plantio18.webp',
        'dicaRegional' => 'Frutíferas clássicas dos quintais de Taubaté: as cascas de banana viram bolos, pães e farofas, e a casca do mamão vira a Geleia do Pensamento Verde!'
    ]
];

// ===================================================================
// 5. BANCO DE RECEITAS REAIS (IMAGENS REAIS E CORRETAS PARA CADA PRATO)
// ===================================================================
$DADOS_RECEITAS = [
    // --- BLOCO 1: PORTAL PENSAMENTO VERDE ---
    [
        'titulo' => 'Bolo de Casca de Banana com Canela',
        'categoria' => 'Aproveitamento Integral',
        'ingredientes' => 'Casca de Banana (4 bananas maduras higienizadas), Ovos (3 unidades), Leite ou bebida vegetal (2 xícaras), Margarina ou Óleo (2 colheres de sopa), Açúcar (2 xícaras), Farinha de Rosca ou Trigo/Aveia (3 xícaras), Fermento químico (1 colher de sopa), Canela em pó para polvilhar',
        'modo_preparo' => '1. Lave muito bem as cascas de banana e pique em pedaços. 2. Bata no liquidificador as cascas de banana, os ovos, o leite e o óleo/margarina até obter um creme liso. 3. Transfira para uma tigela e misture a farinha, o açúcar e, por último, o fermento em pó. 4. Despeje em forma untada, polvilhe açúcar com canela e asse em forno médio (180°C) por cerca de 35 minutos.',
        'dica_sustentavel' => 'Receita clássica do portal Pensamento Verde: a casca da banana é rica em potássio e concentra mais que o dobro de fibras da polpa (Tabela TACO/Unicamp), evitando resíduo orgânico.',
        'autor' => 'Portal Pensamento Verde',
        'imagem_url' => 'img/receita1.webp',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Patê Cremoso de Talos de Beterraba com Ricota',
        'categoria' => 'Aproveitamento Integral',
        'ingredientes' => 'Talos de Beterraba/Espinafre higienizados a gosto (1 maço), Ricota amassada (1 porção de 150g), Cebola picada (1/2 unidade), Alho picado (1 dente), Azeite ou Óleo (1 colher de sopa), Sal e Cheiro-verde a gosto',
        'modo_preparo' => '1. Lave bem e pique finamente os talos de beterraba. 2. Refogue os talos em uma panela com o óleo/azeite, a cebola e o alho até amaciarem. 3. Misture ou processe com a ricota até chegar ao ponto de purê cremoso. 4. Deixe esfriar na geladeira e sirva com torradas de pão amanhecido.',
        'dica_sustentavel' => 'Receita oficial do Pensamento Verde: aproveita os talos arroxeados da beterraba, ricos em ferro, potássio e antioxidantes betalainas que normalmente vão para o lixo.',
        'autor' => 'Portal Pensamento Verde',
        'imagem_url' => 'img/receita2.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Pizza Fingida de Pão Amanhecido no Forno',
        'categoria' => 'Rápida & Econômica',
        'ingredientes' => 'Pão amanhecido fatiado (3 pães franceses), Tomate em molho caseiro (1 xícara de chá), Ovos (2 unidades), Cebola picada (1/2 unidade), Salsinha e Azeitonas picadas a gosto, Queijo ralado opcional, Sal a gosto',
        'modo_preparo' => '1. Sem precisar untar, forre o fundo de uma assadeira com as fatias de pão amanhecido. 2. Espalhe o molho de tomate sobre os pães e distribua a cebola, a salsinha e as azeitonas. 3. Bata as claras em neve, junte as gemas com uma pitada de sal e cubra os pães com esse creme aerado. 4. Leve ao forno médio (180°C) por cerca de 20 minutos até dourar.',
        'dica_sustentavel' => 'Destaque do portal Pensamento Verde: transforma pães franceses endurecidos em um lanche de forno rápido sem precisar sovar massa nova.',
        'autor' => 'Portal Pensamento Verde',
        'imagem_url' => 'img/receita3.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Suco Refrescante e Nutritivo de Casca de Manga',
        'categoria' => 'Aproveitamento Integral',
        'ingredientes' => 'Casca de Manga (cascas bem lavadas de 3 mangas maduras e grandes), Água gelada (1 litro), Hortelã fresca opcional, Açúcar ou Mel a gosto',
        'modo_preparo' => '1. Escove e lave muito bem as mangas maduras antes de descascar. 2. Bata as cascas no liquidificador com apenas 500 ml de água até triturar completamente, extraindo o máximo de nutrientes e fibras. 3. Coe, adicione os outros 500 ml de água gelada na jarra, adoce a gosto e sirva.',
        'dica_sustentavel' => 'Ensinada pelo Pensamento Verde: a casca da manga concentra vitamina C, carotenoides e fibras solúveis que enriquecem o suco.',
        'autor' => 'Portal Pensamento Verde',
        'imagem_url' => 'img/receita4.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Panquecas Verdes Nutritivas de Talos de Brócolis',
        'categoria' => 'Aproveitamento Integral',
        'ingredientes' => 'Talos de Brócolis/Couve picados (1 xícara de chá), Leite (1 xícara de chá), Farinha de Trigo ou Aveia (3/4 de xícara de chá), Ovos (2 unidades), Margarina ou Azeite (1 colher de sopa), Sal a gosto',
        'modo_preparo' => '1. Bata os talos de brócolis higienizados com o leite no liquidificador. 2. Passe a mistura por uma peneira (ou mantenha as fibras) e volte ao liquidificador com os ovos, a farinha, a margarina e o sal. 3. Aqueça uma frigideira levemente untada, despeje uma concha da massa verde, doure dos dois lados e recheie com sobras de frango ou legumes.',
        'dica_sustentavel' => 'Receita do Pensamento Verde: aproveita o talo central do brócolis (que representa quase metade do peso do maço comprado na feira) rico em cálcio e ácido fólico.',
        'autor' => 'Portal Pensamento Verde',
        'imagem_url' => 'img/receita5.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Geleia Caseira de Casca de Mamão',
        'categoria' => 'Vegana/Vegetariana',
        'ingredientes' => 'Casca de Laranja/Mamão (cascas bem lavadas de 1 mamão médio maduro), Água suficiente para cobrir, Açúcar demerara ou cristal (metade da medida da massa cozida), Cravo-da-índia opcional',
        'modo_preparo' => '1. Lave muito bem o mamão com uma escovinha e retire as cascas. 2. Coloque as cascas em uma panela, cubra com água e cozinhe até ficarem bem macias e desmanchando. 3. Bata no liquidificador e passe pela peneira. 4. Meça a polpa obtida, adicione metade dessa medida em açúcar e leve ao fogo baixo mexendo até atingir ponto de geleia brilhante.',
        'dica_sustentavel' => 'Clássico do Pensamento Verde: a casca do mamão é rica em papaína e pectina natural, dando ponto de geleia rapidamente sem conservantes industriais.',
        'autor' => 'Portal Pensamento Verde',
        'imagem_url' => 'img/receita6.webp',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Bolinho Nutritivo de Talos, Folhas e Cascas',
        'categoria' => 'Aproveitamento Integral',
        'ingredientes' => 'Talos de Brócolis/Couve, Talos de Beterraba/Espinafre bem picados (2 xícaras), Ovos (2 unidades), Cebola picada (1 unidade), Tomate picado (1 unidade), Farinha de Trigo ou Aveia (5 colheres de sopa), Fermento (1 colher de chá), Azeite e Sal',
        'modo_preparo' => '1. Higienize e pique finamente todos os talos e folhas que sobraram do preparo do almoço. 2. Refogue rapidamente com cebola e tomate no azeite por 3 minutos e deixe amornar. 3. Misture os ovos batidos, a farinha, o sal e o fermento. 4. Modele os bolinhos e asse em assadeira untada a 180°C por 20 minutos.',
        'dica_sustentavel' => 'Clássico do Pensamento Verde para aproveitamento integral: evita o descarte de até 40% do peso bruto das hortaliças compradas na feira.',
        'autor' => 'Portal Pensamento Verde',
        'imagem_url' => 'img/receita7.avif',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Refogado (Carne Louca Vegana) de Casca de Melancia',
        'categoria' => 'Vegana/Vegetariana',
        'ingredientes' => 'Casca de Melancia (parte branca firme ralada de 1/2 melancia), Tomate maduro picado (2 unidades), Cebola picada (1 unidade), Alho (2 dentes), Pão amanhecido tostado (para servir), Azeite (2 colheres de sopa), Cheiro-verde, Páprica e Sal',
        'modo_preparo' => '1. Retire apenas a película verde escura externa da melancia e rale toda a entrecasca branca no ralo grosso. 2. Refogue a cebola e o alho no azeite com páprica. 3. Adicione a entrecasca ralada e os tomates picados, tampe a panela e cozinhe em fogo médio por 15 minutos até amaciar. 4. Finalize com cheiro-verde e sirva no pão amanhecido tostado.',
        'dica_sustentavel' => 'Presente nos portais Pensamento Verde e eConsciente: aproveita a entrecasca branca (que representa quase metade do peso da fruta) rica em citrulina e fibras.',
        'autor' => 'Pensamento Verde & eConsciente',
        'imagem_url' => 'img/receita8.jpg',
        'status' => 'aprovado'
    ],

    // --- BLOCO 2: PORTAL ECONSCIENTE ---
    [
        'titulo' => 'Pão Caseiro Integral de Casca de Banana',
        'categoria' => 'Aproveitamento Integral',
        'ingredientes' => 'Casca de Banana e Polpa (6 bananas maduras com casca higienizada), Farinha de Trigo Integral (3 xícaras), Ovos (1 unidade), Água morna (1 xícara), Óleo vegetal (1/2 xícara), Açúcar mascavo (1 colher de sopa), Fermento biológico (1 colher de sopa), Sal (1 pitada)',
        'modo_preparo' => '1. Descasque as bananas, pique as cascas e corte a polpa em rodelas. 2. Bata no liquidificador as cascas de banana, a água, o ovo e o óleo até obter um creme homogêneo. 3. Em uma tigela grande, misture esse creme com a farinha integral, o açúcar mascavo, o sal e o fermento. 4. Incorpore as rodelas de banana, despeje em forma de pão untada e asse a 200°C por 30 minutos.',
        'dica_sustentavel' => 'Receita nº 1 do portal eConsciente: utiliza 100% da fruta (polpa e casca) combinada com farinha integral, gerando zero resíduo orgânico.',
        'autor' => 'Portal eConsciente',
        'imagem_url' => 'img/receita9.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Doce Caseiro de Casca de Laranja com Especiarias',
        'categoria' => 'Vegana/Vegetariana',
        'ingredientes' => 'Casca de Laranja/Mamão (4 laranjas grandes bem lavadas), Açúcar mascavo ou demerara (2 xícaras), Água (1 xícara), Suco de Limão (1 colher de sopa), Canela em pau (1 unidade) e Cravos-da-índia a gosto',
        'modo_preparo' => '1. Corte as cascas de laranja em tirinhas finas. 2. Ferva as cascas em água por 5 minutos, escorra e repita o processo 2 vezes para eliminar o amargor. 3. Em outra panela, faça uma calda com 1 xícara de água, o açúcar mascavo, a canela e os cravos. 4. Junte as cascas e cozinhe em fogo baixo por 30 a 40 minutos até ficarem macias e brilhantes. Finalize com o suco de limão.',
        'dica_sustentavel' => 'Receita nº 2 do portal eConsciente: reaproveita as cascas do suco de laranja natural, ricas em óleos essenciais cítricos e flavonoides antioxidantes.',
        'autor' => 'Portal eConsciente',
        'imagem_url' => 'img/receita10.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Geleia Aromática de Casca de Abacaxi com Canela',
        'categoria' => 'Vegana/Vegetariana',
        'ingredientes' => 'Casca de Abacaxi (casca de 1 abacaxi inteiro escovado e picado), Água (2 xícaras), Açúcar mascavo ou demerara (1 xícara), Suco de 1 Limão, Canela em pau (1 unidade) e Cravos-da-índia (2 unidades)',
        'modo_preparo' => '1. Ferva as cascas de abacaxi picadas com as 2 xícaras de água por 20 minutos até amolecerem bem. 2. Deixe amornar, bata tudo no liquidificador e coe para retirar apenas as fibras mais duras. 3. Volte o caldo espesso para a panela com o açúcar, o suco de limão, a canela e os cravos. 4. Cozinhe em fogo baixo por 25 minutos até encorpar em ponto de geleia.',
        'dica_sustentavel' => 'Receita nº 4 do portal eConsciente: a casca do abacaxi concentra bromelina (enzima digestiva natural) e perfuma a cozinha inteira.',
        'autor' => 'Portal eConsciente',
        'imagem_url' => 'img/receita11.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Chips Assados de Casca de Batata com Alecrim',
        'categoria' => 'Vegana/Vegetariana',
        'ingredientes' => 'Batata (cascas higienizadas de 5 batatas inglesas ou doces), Azeite de oliva (2 colheres de sopa), Alecrim fresco ou desidratado (1 colher de chá), Páprica defumada (1 colher de chá), Sal e Pimenta-do-reino a gosto',
        'modo_preparo' => '1. Escove e lave muito bem as batatas antes de descascar. 2. Seque completamente as cascas com um pano limpo (segredo para ficarem super crocantes). 3. Em uma assadeira, envolva as cascas com o azeite, o alecrim, a páprica e o sal. 4. Espalhe sem sobrepor e asse a 200°C por 15 a 20 minutos, virando na metade do tempo até dourarem.',
        'dica_sustentavel' => 'Receita nº 5 do portal eConsciente: substitui salgadinhos industrializados embalados em plástico metalizado BOPP (não reciclável) por um petisco rico em fibras e ferro.',
        'autor' => 'Portal eConsciente',
        'imagem_url' => 'img/receita12.JPG',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Bolo Fofinho de Pão Amanhecido com Açúcar Mascavo',
        'categoria' => 'Rápida & Econômica',
        'ingredientes' => 'Pão amanhecido picado (3 pães franceses), Leite (2 xícaras), Ovos (3 unidades), Açúcar mascavo (1 xícara), Manteiga derretida (2 colheres de sopa), Canela em pó (1 colher de chá), Fermento químico (1 colher de sopa)',
        'modo_preparo' => '1. Pique os pães amanhecidos em uma tigela, regue com o leite e deixe hidratar por 10 minutos. 2. Bata no liquidificador o pão hidratado com os ovos, o açúcar mascavo, a manteiga e a canela até obter uma massa lisa. 3. Misture delicadamente o fermento em pó, despeje em forma untada e asse a 180°C por 35 a 40 minutos.',
        'dica_sustentavel' => 'Receita nº 6 do portal eConsciente: substitui 100% da farinha de trigo nova por pães que seriam descartados, economizando trigo, água e energia.',
        'autor' => 'Portal eConsciente',
        'imagem_url' => 'img/receita13.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Omelete Dourada de Talos de Espinafre e Alho',
        'categoria' => 'Rápida & Econômica',
        'ingredientes' => 'Talos de Beterraba/Espinafre higienizados e picados (1 maço), Ovos (3 unidades), Alho picado (1 dente), Tomate picado opcional (1/2 unidade), Azeite (1 colher de sopa), Sal e Pimenta-do-reino a gosto',
        'modo_preparo' => '1. Em uma frigideira antiaderente, aqueça o azeite e refogue o alho e os talos de espinafre picadinhos por 3 minutos até ficarem macios. 2. Bata os ovos com sal e pimenta e despeje sobre os talos na frigideira. 3. Cozinhe em fogo baixo com tampa até firmar e doure dos dois lados.',
        'dica_sustentavel' => 'Receita nº 7 do portal eConsciente: os talos do espinafre são ricos em ferro, magnésio e fibras insolúveis, garantindo saciedade em menos de 10 minutos.',
        'autor' => 'Portal eConsciente',
        'imagem_url' => 'img/receita14.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Mexidão Nutritivo com Arroz Amanhecido e Cúrcuma',
        'categoria' => 'Tradicional Regional',
        'ingredientes' => 'Arroz cozido de sobra (2 xícaras), Feijão cozido de sobra (1/2 xícara), Frango ou Carne de sobra picada (1/2 xícara), Cenoura ralada (1 unidade), Talos de Brócolis/Couve picados (1 xícara), Ovos (1 unidade), Cebola, Alho, Cúrcuma (1 colher de chá) e Cheiro-verde',
        'modo_preparo' => '1. Refogue a cebola e o alho no azeite, adicione a cenoura ralada e os talos/folhas verdes picados. 2. Acrescente o arroz cozido amanhecido, o feijão, a sobra de frango/carne e tempere com cúrcuma e páprica. 3. Abra um espaço no centro da frigideira, quebre o ovo, mexa até cozinhar e finalize com cheiro-verde fresco.',
        'dica_sustentavel' => 'Receita nº 8 do portal eConsciente: reúne pequenas sobras de proteínas, grãos e verduras em uma única panela, economizando gás e água na lavagem de louças.',
        'autor' => 'Portal eConsciente',
        'imagem_url' => 'img/receita15.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Farofa Crocante de Talos, Folhas e Cúrcuma',
        'categoria' => 'Aproveitamento Integral',
        'ingredientes' => 'Talos de Brócolis/Couve e Talos de Beterraba/Espinafre bem picados (1 e 1/2 xícara), Cenoura ralada com casca (1/2 unidade), Tomate picado (1 unidade), Farinha de Mandioca ou de Milho (1 xícara), Cebola (1 unidade), Alho (2 dentes), Azeite ou Manteiga (2 colheres de sopa), Cúrcuma e Cheiro-verde',
        'modo_preparo' => '1. Aqueça o azeite ou manteiga na frigideira e doure a cebola e o alho. 2. Adicione todos os talos picados, a cenoura ralada e o tomate, refogando por 5 minutos até ficarem macios. 3. Tempere com cúrcuma, sal e pimenta. 4. Acrescente a farinha de mandioca ou de milho aos poucos, mexendo em fogo baixo até ficar bem crocante.',
        'dica_sustentavel' => 'Receita nº 9 do portal eConsciente: transforma os talos que sobram na tábua de corte em um acompanhamento rico em fibras prebióticas e vitaminas.',
        'autor' => 'Portal eConsciente',
        'imagem_url' => 'img/receita16.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Rabanada de Forno Saudável com Pão Amanhecido',
        'categoria' => 'Rápida & Econômica',
        'ingredientes' => 'Pão amanhecido cortado em fatias de 2cm (4 pães franceses), Leite ou bebida vegetal (2 xícaras), Ovos (2 unidades), Açúcar ou Mel (3 colheres de sopa), Canela em pó (1 colher de sopa), Essência de baunilha (1 colher de chá)',
        'modo_preparo' => '1. Bata em uma tigela o leite, os ovos, o açúcar, a baunilha e metade da canela. 2. Mergulhe rapidamente as fatias de pão amanhecido na mistura sem encharcar demais. 3. Disponha em assadeira untada e asse a 180°C por 20 a 25 minutos, virando na metade do tempo para dourar os dois lados. 4. Polvilhe açúcar com canela ainda quente.',
        'dica_sustentavel' => 'Receita nº 10 do portal eConsciente: além de salvar o pão endurecido, assar no forno elimina o uso de óleo de fritura, evitando a poluição de litros de água.',
        'autor' => 'Portal eConsciente',
        'imagem_url' => 'img/receita17.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Molho Pesto de Rama (Folhas) de Cenoura e Talos',
        'categoria' => 'Vegana/Vegetariana',
        'ingredientes' => 'Cenoura (folhas/ramas verdes higienizadas de 1 maço), Talos de Brócolis/Couve ou Salsinha (1/2 xícara), Azeite de oliva (1/2 xícara), Alho (1 dente), Castanhas, Nozes ou Amendoim torrado (1/2 xícara), Suco de 1/2 Limão, Sal a gosto',
        'modo_preparo' => '1. Lave bem as folhas (ramas) da cenoura e seque. 2. No processador ou liquidificador, coloque as ramas de cenoura, os talos, o alho, o amendoim (ou castanhas) e o suco de limão. 3. Bata pulsando enquanto adiciona o azeite em fio até atingir consistência de pesto rústico. 4. Sirva com massas, torradas de pão amanhecido ou saladas.',
        'dica_sustentavel' => 'Receita destaque do portal eConsciente: as ramas de cenoura geralmente são descartadas nas feiras, mas possuem mais cálcio e vitamina C que a própria raiz.',
        'autor' => 'Portal eConsciente',
        'imagem_url' => 'img/receita18.jpg',
        'status' => 'aprovado'
    ],

    // --- BLOCO 3: PORTAL RECEITERIA & EMBRAPA ---
    [
        'titulo' => 'Bolinho Crocante para Reaproveitar Sobras de Arroz',
        'categoria' => 'Rápida & Econômica',
        'ingredientes' => 'Arroz cozido (2 xícaras de sobras do dia anterior), Ovos (2 unidades), Cenoura ralada com casca (1/2 unidade), Tomate picado sem sementes (1 unidade), Cebola picada (1/2 unidade), Queijo ralado (3 colheres de sopa), Cheiro-verde, Sal, Farinha de Trigo ou Aveia até dar ponto (1/2 xícara)',
        'modo_preparo' => '1. Em uma tigela funda, misture o arroz cozido amanhecido, a cenoura ralada, o tomate, a cebola, o cheiro-verde e o queijo. 2. Adicione os ovos batidos e o sal, misturando bem. 3. Acrescente a farinha aos poucos até formar uma massa firme para modelar com duas colheres. 4. Asse em forno pré-aquecido a 200°C ou na Airfryer por 18 minutos até dourar.',
        'dica_sustentavel' => 'Receita nº 1 da Receiteria (Sobras): o arroz é o alimento mais descartado nos lares brasileiros; reaproveitá-lo economiza milhares de litros de água virtual.',
        'autor' => 'Portal Receiteria (Sobras)',
        'imagem_url' => 'img/receita19.webp',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Bolo Caseiro de Casca de Manga Fofinho',
        'categoria' => 'Aproveitamento Integral',
        'ingredientes' => 'Casca de Manga (cascas picadas de 2 mangas grandes maduras), Água (1 xícara), Ovos (3 unidades), Óleo (1/2 xícara), Açúcar (1 e 1/2 xícara), Farinha de Trigo (2 e 1/2 xícaras), Fermento em pó (1 colher de sopa)',
        'modo_preparo' => '1. Higienize bem as mangas, retire as cascas e bata no liquidificador com a água, os ovos, o óleo e o açúcar até obter um líquido alaranjado uniforme. 2. Transfira para uma tigela e incorpore a farinha de trigo peneirada. 3. Adicione o fermento por último, despeje em forma untada e asse a 180°C por cerca de 40 minutos.',
        'dica_sustentavel' => 'Receita nº 2 da Receiteria (Sobras): aproveita integralmente as cascas de manga da época de safra, conferindo cor dourada, aroma tropical e alto teor de fibras ao bolo.',
        'autor' => 'Portal Receiteria (Sobras)',
        'imagem_url' => 'img/receita20.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Doce Caseiro de Casca de Melancia em Calda',
        'categoria' => 'Tradicional Regional',
        'ingredientes' => 'Casca de Melancia (parte branca firme cortada em cubos de 1/2 melancia), Açúcar (1 e 1/2 xícara), Água (2 xícaras), Cravos-da-índia (6 unidades), Canela em pau (1 unidade), Casca fina de 1/2 Limão',
        'modo_preparo' => '1. Retire a película verde escura externa e corte a entrecasca branca da melancia em cubinhos regulares. 2. Em uma panela, leve os cubos ao fogo com a água, o açúcar, a canela, os cravos e a casca de limão. 3. Cozinhe em fogo brando por cerca de 50 minutos até os cubinhos ficarem translúcidos e macios na calda. 4. Sirva frio como sobremesa tradicional.',
        'dica_sustentavel' => 'Receita nº 3 da Receiteria (Sobras): resgata a doçaria tradicional do interior paulista e evita o descarte da casca pesada da melancia.',
        'autor' => 'Portal Receiteria (Sobras)',
        'imagem_url' => 'img/receita21.webp',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Farofa Brasileira de Casca de Banana',
        'categoria' => 'Tradicional Regional',
        'ingredientes' => 'Casca de Banana madura bem lavada e picada em cubinhos (4 unidades), Cebola picada (1 unidade), Alho picado (2 dentes), Tomate sem sementes picado (1 unidade), Farinha de Mandioca torrada (1 e 1/2 xícara), Azeite ou Manteiga (3 colheres de sopa), Cheiro-verde e Sal',
        'modo_preparo' => '1. Lave e seque as cascas de banana, picando em tirinhas ou cubos pequenos. 2. Aqueça o azeite ou manteiga e refogue a cebola e o alho até dourarem. 3. Junte as cascas de banana picadas e refogue por 5 minutos até ficarem macias. 4. Adicione o tomate, a farinha de mandioca, o sal e o cheiro-verde, mexendo até ficar crocante.',
        'dica_sustentavel' => 'Receita nº 8 da Receiteria (Sobras): acompanhamento tipicamente brasileiro que enriquece o almoço com fibras, potássio, magnésio e triptofano.',
        'autor' => 'Portal Receiteria (Sobras)',
        'imagem_url' => 'img/receita22.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Torta Salgada de Liquidificador com Sobras de Legumes e Frango',
        'categoria' => 'Rápida & Econômica',
        'ingredientes' => 'Frango ou Carne de sobra desfiada/picada (1 e 1/2 xícara), Tomate picado (1 unidade), Cenoura ralada (1 unidade), Batata cozida em cubinhos com casca (1 unidade), Ovos (3 unidades), Leite (1 e 1/2 xícara), Azeite ou Óleo (1/2 xícara), Farinha de Trigo (2 xícaras), Fermento (1 colher de sopa)',
        'modo_preparo' => '1. Misture em uma tigela todas as sobras temperadas da geladeira (frango desfiado, batata cozida, cenoura, tomate, milho ou ervilha). 2. Bata no liquidificador os ovos, o leite, o óleo, a farinha e o sal, adicionando o fermento por último. 3. Despeje metade da massa em assadeira untada, espalhe o recheio de sobras, cubra com o restante da massa e asse a 180°C por 35 minutos.',
        'dica_sustentavel' => 'Receita nº 9 da Receiteria (Sobras): permite limpar a gaveta da geladeira no fim de semana, transformando pequenos potes de legumes e proteínas em uma refeição completa.',
        'autor' => 'Portal Receiteria (Sobras)',
        'imagem_url' => 'img/receita23.webp',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Arroz de Forno Cremoso com Sobras da Geladeira',
        'categoria' => 'Rápida & Econômica',
        'ingredientes' => 'Arroz cozido (3 xícaras de sobras), Frango ou Carne de sobra desfiada (1 e 1/2 xícara), Tomate picado (2 unidades), Cenoura ralada (1 unidade), Talos de Brócolis/Couve picados (1/2 xícara), Cebola e Alho picados, Molho de tomate caseiro (1 xícara), Azeite',
        'modo_preparo' => '1. Refogue a cebola e o alho no azeite, adicione o frango desfiado (ou sobras de legumes), os talos picados, a cenoura e o tomate. 2. Junte o molho de tomate caseiro e deixe apurar por 3 minutos. 3. Misture o arroz cozido, transfira para um refratário untado e leve ao forno a 180°C por 15 minutos para gratinar.',
        'dica_sustentavel' => 'Receita nº 11 da Receiteria (Sobras): transforma sobras de arroz e frango assado do domingo em um prato principal novo e suculento.',
        'autor' => 'Portal Receiteria (Sobras)',
        'imagem_url' => 'img/receita24.webp',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Sopa Reconfortante de Sobras de Feijão com Macarrão',
        'categoria' => 'Tradicional Regional',
        'ingredientes' => 'Feijão cozido de sobra com caldo (3 xícaras), Cenoura e Batata com casca em cubinhos (1 unidade de cada), Talos de Brócolis/Couve ou Salsinha picados (1/2 xícara), Macarrão padre-nosso ou conchinha (1 xícara), Cebola (1/2 unidade), Alho (2 dentes), Água quente (4 xícaras), Azeite',
        'modo_preparo' => '1. Bata metade do feijão cozido no liquidificador com a água para criar um caldo encorpado e mantenha a outra metade em grãos. 2. Refogue a cebola e o alho no azeite, junte a cenoura, a batata e os talos picados. 3. Despeje o caldo de feijão e os grãos, deixe cozinhar por 10 minutos, adicione o macarrão e cozinhe até ficar al dente.',
        'dica_sustentavel' => 'Receita nº 14 da Receiteria (Sobras): aproveita aquele feijão que ficou no fundo da panela ou congelado em potes, rendendo 4 pratos nutritivos em 20 minutos.',
        'autor' => 'Portal Receiteria (Sobras)',
        'imagem_url' => 'img/receita25.webp',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Croutons Crocantes de Frigideira com Pão Amanhecido',
        'categoria' => 'Vegana/Vegetariana',
        'ingredientes' => 'Pão amanhecido cortado em cubos pequenos (3 pães franceses ou fatias de pão de forma), Azeite de oliva (3 colheres de sopa), Alho amassado (1 dente), Orégano, Alecrim e Páprica (1 colher de chá), Sal a gosto',
        'modo_preparo' => '1. Corte os pães amanhecidos em cubos de cerca de 1,5 cm. 2. Aqueça uma frigideira larga em fogo médio-baixo com o azeite e o alho. 3. Adicione os cubos de pão, polvilhe o orégano, o alecrim e o sal, e mexa continuamente por 10 a 12 minutos até ficarem dourados e super crocantes por fora. 4. Guarde em pote de vidro fechado para usar em sopas e saladas.',
        'dica_sustentavel' => 'Receita nº 17 da Receiteria (Sobras): preparada na boca do fogão em apenas 15 minutos, salvando pães endurecidos sem precisar ligar o forno.',
        'autor' => 'Portal Receiteria (Sobras)',
        'imagem_url' => 'img/receita26.webp',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Tutu de Feijão Rápido com Sobras de Feijão Cozido',
        'categoria' => 'Tradicional Regional',
        'ingredientes' => 'Feijão cozido de sobra (3 xícaras com caldo), Cebola picada (1 unidade), Alho amassado (2 dentes), Tomate picado (1 unidade), Farinha de Mandioca ou de Milho (1/2 a 3/4 de xícara), Talos de Brócolis/Couve fatiados para acompanhar, Azeite e Cheiro-verde',
        'modo_preparo' => '1. Em uma panela, doure a cebola e o alho no azeite e refogue o tomate picado. 2. Acrescente as sobras de feijão cozido (pode amassar parte dos grãos com o garfo ou bater rapidamente). 3. Quando ferver, abaixe o fogo e adicione a farinha de mandioca aos poucos, mexendo vigorosamente até formar um creme aveludado. Finalize com cheiro-verde.',
        'dica_sustentavel' => 'Receita nº 27 da Receiteria (Sobras): prato símbolo da culinária tropeira do Vale do Paraíba que dá vida nova ao feijão de dias anteriores.',
        'autor' => 'Portal Receiteria (Sobras)',
        'imagem_url' => 'img/receita27.webp',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Pudim Clássico de Boca de Fogão com Pão Amanhecido',
        'categoria' => 'Rápida & Econômica',
        'ingredientes' => 'Pão amanhecido (3 a 4 pães franceses endurecidos picados), Ovos (3 unidades), Leite (500 ml), Açúcar (1 xícara para a massa + 1/2 xícara para a calda), Canela em pó ou Essência de baunilha e Coco ralado opcional',
        'modo_preparo' => '1. Deixe o pão amanhecido picado de molho no leite por 15 minutos até amolecer. 2. Bata no liquidificador o pão com leite, os ovos, o açúcar e a canela por 3 minutos. 3. Despeje em uma forma caramelizada com açúcar derretido. 4. Cozinhe em banho-maria na boca do fogão (tampado) por 30 minutos.',
        'dica_sustentavel' => 'Destaque da Receiteria: o pão francês endurecido é um dos itens mais jogados fora nas casas; cozinhar em banho-maria na boca do fogão ainda economiza gás.',
        'autor' => 'Portal Receiteria (Sobras)',
        'imagem_url' => 'img/receita28.jpg',
        'status' => 'aprovado'
    ],
    [
        'titulo' => 'Nhoque Caseiro de Batata com Cascas e Talos Verdes',
        'categoria' => 'Aproveitamento Integral',
        'ingredientes' => 'Batata cozida com casca (4 unidades médias amassadas), Talos de Brócolis/Couve ou Espinafre processados (1/2 xícara), Ovos (1 unidade), Farinha de Trigo ou Aveia (1 xícara), Tomate para o molho (3 unidades), Azeite e Sal a gosto',
        'modo_preparo' => '1. Cozinhe as batatas inteiras com casca e amasse ainda quentes. 2. Bata os talos verdes com um fio de água e misture ao purê junto com o ovo e o sal. 3. Adicione a farinha aos poucos até formar rolinhos, corte os nhoques e cozinhe em água fervente até subirem. 4. Sirva com molho de tomates maduros.',
        'dica_sustentavel' => 'Receita SESC Mesa Brasil: manter a casca da batata e incorporar talos verdes reduz a quantidade de farinha refinada e triplica o teor de fibras do prato.',
        'autor' => 'Rede Mesa Brasil SESC',
        'imagem_url' => 'img/receita29.jpg',
        'status' => 'aprovado'
    ]
];

// ===================================================================
// 6. LOCAIS SUSTENTÁVEIS DE TAUBATÉ E REGIÃO (ENDEREÇOS E LOCAIS 100% REAIS NO MAPS)
// ===================================================================
$DADOS_LOCAIS = [
    [
        'nome' => 'Mercado Municipal de Taubaté',
        'tipo' => 'Mercado & Agricultura Local',
        'cidade' => 'Taubaté',
        'descricao' => 'Tradicional Mercadão Municipal do Centro de Taubaté e palco da feira livre de domingo. Reúne bancas históricas com hortifrútis da época, grãos e temperos a granel (sem embalagens plásticas), mel regional, rapadura e queijos artesanais da Serra da Mantiqueira.',
        'endereco' => 'Praça Dr. Paula de Tolêdo, 50 - Centro, Taubaté - SP, 12010-050',
        'link_maps' => 'https://www.google.com/maps/search/?api=1&query=Mercado+Municipal+de+Taubate+Praca+Dr+Paula+de+Toledo+50+Taubate+SP',
        'imagem_url' => 'img/local1.jpg'
    ],
    [
        'nome' => 'Mercatau Mercado Atacadista de Taubaté',
        'tipo' => 'Feira do Produtor & Cooperativa',
        'cidade' => 'Taubaté',
        'descricao' => 'Verdadeiro entreposto e feira dos produtores rurais de Taubaté no Jardim Eulália (atacado e varejo). Espaço onde agricultores familiares da região comercializam verduras, frutas e raízes colhidas no dia diretamente com a população, fortalecendo a economia local e reduzindo o food miles.',
        'endereco' => 'Rua Professora Escolástica Maria de Jesus, 100 - Jardim Eulália, Taubaté - SP, 12091-050',
        'link_maps' => 'https://www.google.com/maps/search/?api=1&query=Mercatau+Mercado+Atacadista+de+Taubate+Rua+Professora+Escolastica+Maria+de+Jesus+100+Taubate+SP',
        'imagem_url' => 'img/local1.webp'
    ],
    [
        'nome' => 'Sesc Taubaté',
        'tipo' => 'Projeto Ambiental & Social (Mesa Brasil)',
        'cidade' => 'Taubaté',
        'descricao' => 'Sede oficial do programa Mesa Brasil Sesc em Taubaté, referência regional no combate à fome e ao desperdício de alimentos. Também realiza oficinas abertas de compostagem doméstica, hortas urbanas e culinária de aproveitamento integral.',
        'endereco' => 'Av. Eng. Milton de Alvarenga Peixoto, 1264 - Esplanada Santa Terezinha, Taubaté - SP, 12052-230',
        'link_maps' => 'https://www.google.com/maps/search/?api=1&query=Sesc+Taubate+Av+Eng+Milton+de+Alvarenga+Peixoto+1264+Taubate+SP',
        'imagem_url' => 'img/local3.webp'
    ],
    [
        'nome' => 'Parque Vale do Itaim',
        'tipo' => 'Horta Educativa & Viveiro Municipal',
        'cidade' => 'Taubaté',
        'descricao' => 'Unidade municipal de conservação e educação ambiental de Taubaté que abriga viveiro educativo de mudas nativas e frutíferas da Mata Atlântica, meliponário (abelhas nativas sem ferrão), trilhas ecológicas e ações pedagógicas sobre solos e águas.',
        'endereco' => 'R. José de Lima, 450 - Jardim Sandra Maria, Taubaté - SP, 12081-340',
        'link_maps' => 'https://www.google.com/maps/search/?api=1&query=Parque+Vale+do+Itaim+Rua+Jose+de+Lima+450+Taubate+SP',
        'imagem_url' => 'img/local4.jpg'
    ],
    [
        'nome' => 'Horto Municipal De Tremembé',
        'tipo' => 'Horta & Viveiro Agroecológico',
        'cidade' => 'Tremembé',
        'descricao' => 'Espaço público de preservação, viveiro municipal de mudas e educação ambiental em Tremembé, cidade vizinha a Taubaté com forte tradição agrícola e rizícola na várzea do Rio Paraíba do Sul.',
        'endereco' => 'R. Poços de Caldas, 32 - Centro, Tremembé - SP',
        'link_maps' => 'https://www.google.com/maps/search/?api=1&query=Horto+Municipal+De+Tremembe+Rua+Pocos+de+Caldas+32+Tremembe+SP',
        'imagem_url' => 'img/local5.png'
    ],
    [
        'nome' => 'Parque Roberto Burle Marx - Parque da Cidade',
        'tipo' => 'Feira Orgânica & Agroecológica',
        'cidade' => 'São José dos Campos',
        'descricao' => 'Principal parque de São José dos Campos que recebe regularmente feiras de produtores orgânicos e agroecológicos certificados do Vale do Paraíba, com venda direta de hortaliças, frutas sazonais e PANCs livres de agrotóxicos.',
        'endereco' => 'Av. Olivo Gomes, 100 - Santana, São José dos Campos - SP, 12211-420',
        'link_maps' => 'https://www.google.com/maps/search/?api=1&query=Parque+Roberto+Burle+Marx+Parque+da+Cidade+Av+Olivo+Gomes+100+Sao+Jose+dos+Campos+SP',
        'imagem_url' => 'img/local6.jpg'
    ]
];


// ===================================================================
// 7. COMENTÁRIOS INICIAIS DE DEMONSTRAÇÃO (SEED)
// ===================================================================
$DADOS_COMENTARIOS_INICIAIS = [
    [
        'tipo_origem'  => 'receita',
        'item_id'      => 1,
        'nome_usuario' => 'Avaliação Demonstrativa (Equipe 03)',
        'nota'         => 5,
        'texto'        => 'O Bolo de Casca de Banana do Pensamento Verde fica super fofinho e ajuda a aproveitar 100% da fruta em casa.',
        'status'       => 'aprovado'
    ],
    [
        'tipo_origem'  => 'receita',
        'item_id'      => 2,
        'nome_usuario' => 'Visitante Colaborador (Taubaté)',
        'nota'         => 5,
        'texto'        => 'O Patê de Talos de Beterraba com ricota é excelente! Servido com croutons de pão amanhecido evita todo o desperdício.',
        'status'       => 'aprovado'
    ],
    [
        'tipo_origem'  => 'local',
        'item_id'      => 1,
        'nome_usuario' => 'Consumidor Consciente (Taubaté)',
        'nota'         => 5,
        'texto'        => 'Comprar no Mercado Municipal de Taubaté (Mercatau) levando ecobags reduz drasticamente o lixo plástico, além de apoiar os produtores locais!',
        'status'       => 'aprovado'
    ]
];
?>
