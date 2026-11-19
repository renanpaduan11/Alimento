
/**
 * PROJETO ACADÊMICO: SUSTENTA+ — Tecnologia, Informação e Sustentabilidade
 * EQUIPE 03: Alimentação Sustentável — Do campo ao prato: produzir, consumir e desperdiçar
 * ARQUIVO: script.js (Sorteio de 10/30 Mitos, Geladeira Corrigida e Layouts Encaixados)
 */

const RECEITAS_DB      = (window.SUSTENTA_DATA && window.SUSTENTA_DATA.receitas) ? window.SUSTENTA_DATA.receitas : [];
const LOCAIS_DB        = (window.SUSTENTA_DATA && window.SUSTENTA_DATA.locais)   ? window.SUSTENTA_DATA.locais   : [];
const DADOS_MITOS      = (window.SUSTENTA_DATA && window.SUSTENTA_DATA.mitos)    ? window.SUSTENTA_DATA.mitos    : [];
const CATALOGO_PLANTIO = (window.SUSTENTA_DATA && window.SUSTENTA_DATA.plantio)  ? window.SUSTENTA_DATA.plantio  : [];

// Função utilitária para escapar HTML e prevenir XSS
function esc(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

// Remove acentos para comparação precisa de ingredientes e buscas
function normalizarTexto(str) {
    return (str || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

// ===================================================================
// CONTROLE DO MENU RESPONSIVO (MOBILE HAMBÚRGUER)
// ===================================================================
function inicializarMenuMobile() {
    const btnMobile = document.getElementById('btn-mobile-menu');
    const menuMobile = document.getElementById('mobile-menu');

    if (btnMobile && menuMobile) {
        btnMobile.addEventListener('click', () => {
            menuMobile.classList.toggle('hidden');
        });

        menuMobile.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                menuMobile.classList.add('hidden');
            });
        });
    }
}

// ===================================================================
// QUIZ: 30 PERGUNTAS NO BANCO -> SORTEIA 10 A CADA REINÍCIO
// ===================================================================
let placarAcertos = 0;
let mitosRespondidos = {};
let mitosSorteados = [];

function sortearDezMitos() {
    const copia = [...DADOS_MITOS];
    // Embaralhamento Fisher-Yates
    for (let i = copia.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [copia[i], copia[j]] = [copia[j], copia[i]];
    }
    mitosSorteados = copia.slice(0, 10);
}

function renderizarMitos() {
    const grid = document.getElementById('grid-mitos');
    if (!grid) return;

    if (mitosSorteados.length === 0) {
        sortearDezMitos();
    }

    // Ajusta o placar visual para X / 10 (mesmo se o HTML antigo tiver / 30)
    const elPlacar = document.getElementById('placar-acertos');
    if (elPlacar && elPlacar.parentElement) {
        elPlacar.parentElement.innerHTML = `<span id="placar-acertos">${placarAcertos}</span> / 10`;
    }

    // Transforma o grid em 2 colunas (5x2 = 10 cards perfeitamente encaixados, sem card #10 sobrando)
    grid.className = "grid md:grid-cols-2 gap-5";

    grid.innerHTML = mitosSorteados.map((item, idx) => {
        const numOrdem = String(idx + 1).padStart(2, '0');
        return `
        <div id="card-mito-${item.id}" class="card-sustenta rounded-3xl overflow-hidden flex flex-col justify-between bg-white border border-stone-200/80 shadow-sm">
            <div class="grid sm:grid-cols-12 items-stretch h-full">
                <div class="sm:col-span-4 h-40 sm:h-auto relative img-zoom-container">
                    <img src="${esc(item.imagem)}" alt="Questão ${numOrdem}" class="w-full h-full object-cover" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/15 to-transparent"></div>
                    <span class="absolute bottom-3 left-3 text-[10px] font-extrabold uppercase tracking-wider bg-laranja-600 text-white px-2.5 py-1 rounded-full shadow">
                        Questão #${numOrdem}
                    </span>
                </div>

                <div class="sm:col-span-8 p-5 flex flex-col justify-between">
                    <h3 class="font-display font-bold text-sm sm:text-base text-grafite leading-snug mb-4">
                        "${esc(item.pergunta)}"
                    </h3>

                    <div>
                        <div id="botoes-mito-${item.id}" class="grid grid-cols-2 gap-2.5">
                            <button type="button" onclick="responderMito(${item.id}, 'Mito')"
                                    class="py-2 px-3 rounded-xl border-2 border-laranja-600 text-laranja-600 hover:bg-laranja-600 hover:text-white font-bold text-xs transition-colors">
                                <i class="fa-solid fa-xmark mr-1"></i> MITO
                            </button>
                            <button type="button" onclick="responderMito(${item.id}, 'Verdade')"
                                    class="py-2 px-3 rounded-xl border-2 border-oliva-700 text-oliva-700 hover:bg-oliva-700 hover:text-white font-bold text-xs transition-colors">
                                <i class="fa-solid fa-check mr-1"></i> VERDADE
                            </button>
                        </div>

                        <div id="feedback-mito-${item.id}" class="hidden mt-3 p-3 rounded-2xl text-xs leading-relaxed animate-fade-in"></div>
                    </div>
                </div>
            </div>
        </div>
        `;
    }).join('');
}

function responderMito(id, escolha) {
    if (mitosRespondidos[id]) return;
    mitosRespondidos[id] = true;

    const item = mitosSorteados.find(m => parseInt(m.id, 10) === parseInt(id, 10)) ||
                 DADOS_MITOS.find(m => parseInt(m.id, 10) === parseInt(id, 10));
    if (!item) return;

    const acertou = (escolha === item.respostaCorreta);
    if (acertou) {
        placarAcertos++;
        const elPlacar = document.getElementById('placar-acertos');
        if (elPlacar) elPlacar.textContent = placarAcertos;
    }

    const divBotoes = document.getElementById(`botoes-mito-${id}`);
    const divFeedback = document.getElementById(`feedback-mito-${id}`);
    const card = document.getElementById(`card-mito-${id}`);

    if (divBotoes) divBotoes.classList.add('opacity-50', 'pointer-events-none');
    if (divFeedback) divFeedback.classList.remove('hidden');

    if (acertou) {
        card.classList.add('ring-2', 'ring-oliva-600');
        divFeedback.className = "mt-3 p-3 rounded-2xl text-xs leading-relaxed bg-oliva-50 border border-oliva-200 text-oliva-900 animate-fade-in";
        divFeedback.innerHTML = `<div class="font-extrabold text-oliva-800 mb-0.5"><i class="fa-solid fa-circle-check mr-1"></i> Resposta Correta (${esc(item.respostaCorreta)})!</div><p>${esc(item.explicacao)}</p>`;
    } else {
        card.classList.add('ring-2', 'ring-laranja-500');
        divFeedback.className = "mt-3 p-3 rounded-2xl text-xs leading-relaxed bg-laranja-50 border border-laranja-200 text-stone-800 animate-fade-in";
        divFeedback.innerHTML = `<div class="font-extrabold text-laranja-700 mb-0.5"><i class="fa-solid fa-circle-xmark mr-1"></i> A resposta correta é: ${esc(item.respostaCorreta)}</div><p>${esc(item.explicacao)}</p>`;
    }
}

function resetarMitos() {
    placarAcertos = 0;
    mitosRespondidos = {};
    sortearDezMitos();
    renderizarMitos();
}

// ===================================================================
// GELADEIRA INTELIGENTE — MATCH PRECISO DE INGREDIENTES (SEM FALSOS POSITIVOS)
// ===================================================================
function ingredienteBateComReceita(ingredienteUsuario, textoReceitaNormalizado) {
    const ingNorm = normalizarTexto(ingredienteUsuario);

    // Regras específicas para compostos como "Casca de Banana", "Casca de Manga", etc.
    if (ingNorm.includes('casca de banana')) return textoReceitaNormalizado.includes('casca de banana');
    if (ingNorm.includes('casca de manga')) return textoReceitaNormalizado.includes('casca de manga');
    if (ingNorm.includes('casca de abacaxi')) return textoReceitaNormalizado.includes('casca de abacaxi');
    if (ingNorm.includes('casca de melancia')) return textoReceitaNormalizado.includes('casca de melancia');
    if (ingNorm.includes('casca de laranja') || ingNorm.includes('mamao')) {
        return textoReceitaNormalizado.includes('casca de laranja') || textoReceitaNormalizado.includes('casca de mamao');
    }
    if (ingNorm.includes('arroz cozido')) return textoReceitaNormalizado.includes('arroz cozido') || textoReceitaNormalizado.includes('sobras de arroz');
    if (ingNorm.includes('feijao cozido')) return textoReceitaNormalizado.includes('feijao cozido') || textoReceitaNormalizado.includes('sobras de feijao');
    if (ingNorm.includes('pao amanhecido')) return textoReceitaNormalizado.includes('pao amanhecido');
    if (ingNorm.includes('brocolis') || ingNorm.includes('couve')) {
        return textoReceitaNormalizado.includes('brocolis') || textoReceitaNormalizado.includes('couve');
    }
    if (ingNorm.includes('beterraba') || ingNorm.includes('espinafre')) {
        return textoReceitaNormalizado.includes('beterraba') || textoReceitaNormalizado.includes('espinafre');
    }
    if (ingNorm.includes('frango') || ingNorm.includes('carne')) {
        return textoReceitaNormalizado.includes('frango') || textoReceitaNormalizado.includes('carne');
    }

    // Ingredientes simples (Cenoura, Batata, Tomate, Ovos, Leite ou digitados)
    const palavrasIgnoradas = ['de', 'da', 'do', 'com', 'ou', 'em', 'para', 'casca', 'talos', 'sobra', 'cozido'];
    const tokens = ingNorm.split(/[\s\/]+/).filter(t => t.length > 2 && !palavrasIgnoradas.includes(t));
    if (tokens.length === 0) return textoReceitaNormalizado.includes(ingNorm);
    return tokens.some(tok => textoReceitaNormalizado.includes(tok));
}

function atualizarGeladeira() {
    const checkboxes = document.querySelectorAll('.chk-ingrediente:checked');
    const selecionados = Array.from(checkboxes).map(cb => cb.value);
    const inputExtra = document.getElementById('input-ingrediente-extra');

    if (inputExtra && inputExtra.value.trim().length >= 2) {
        inputExtra.value.split(',').forEach(termo => {
            if (termo.trim()) selecionados.push(termo.trim());
        });
    }

    const container = document.getElementById('resultado-geladeira');
    const painelImpacto = document.getElementById('resumo-impacto-geladeira');
    if (!container || !painelImpacto) return;

    if (selecionados.length === 0) {
        painelImpacto.classList.add('hidden');
        container.innerHTML = `
            <div class="col-span-full bg-creme border border-dashed border-stone-300 rounded-2xl p-8 text-center">
                <i class="fa-solid fa-kitchen-set text-3xl text-laranja-500 mb-2"></i>
                <h4 class="font-display font-bold text-base text-grafite">Selecione os ingredientes disponíveis na sua cozinha acima</h4>
                <p class="text-xs text-stone-500 mt-1">Marque opções como "Arroz cozido", "Casca de Banana", "Batata" ou "Pão amanhecido" para ver as receitas sugeridas!</p>
            </div>
        `;
        return;
    }

    const receitasCompativeis = RECEITAS_DB.map(rec => {
        const textoAlvoNorm = normalizarTexto(rec.ingredientes + ' ' + rec.titulo);
        const encontrados = [];

        selecionados.forEach(itemUser => {
            if (ingredienteBateComReceita(itemUser, textoAlvoNorm)) {
                encontrados.push(itemUser);
            }
        });

        const totalIngReceita = Math.max(rec.ingredientes.split(',').length, 4);
        const proporcaoBase = encontrados.length / Math.min(totalIngReceita, Math.max(selecionados.length + 1, 3));
        let compatibilidade = Math.min(98, Math.max(45, Math.round(proporcaoBase * 100) + (encontrados.length * 12)));

        return {
            ...rec,
            encontrados,
            qtdEncontrados: encontrados.length,
            compatibilidade: encontrados.length > 0 ? compatibilidade : 0
        };
    }).filter(r => r.qtdEncontrados > 0).sort((a, b) => b.compatibilidade - a.compatibilidade);

    if (receitasCompativeis.length === 0) {
        painelImpacto.classList.add('hidden');
        container.innerHTML = `
            <div class="col-span-full bg-laranja-50 border border-laranja-200 rounded-2xl p-6 text-center">
                <p class="text-sm font-bold text-laranja-800">Nenhuma receita encontrada com apenas esse ingrediente específico. Que tal compartilhar sua receita no botão "Enviar Minha Receita Sustentável"?</p>
            </div>
        `;
        return;
    }

    // Exibe até 6 receitas em destaque (2 linhas de 3 cards) para manter o layout sempre encaixado
    const exibidas = receitasCompativeis.slice(0, 6);
    const gramasEvitadas = selecionados.length * 250;
    const litrosPoupados = selecionados.length * 480;

    painelImpacto.classList.remove('hidden');
    document.getElementById('texto-compatibilidade-geladeira').textContent =
        `${receitasCompativeis.length} receita(s) compatíveis encontradas (exibindo as ${exibidas.length} mais relevantes)!`;
    document.getElementById('texto-desperdicio-evitado').textContent =
        `Desperdício evitado estimado: ~${gramasEvitadas}g de alimentos reaproveitados com alto teor nutricional.`;
    document.getElementById('badge-agua-salva').textContent =
        `💧 ~${litrosPoupados}L de água virtual preservados`;

    container.innerHTML = exibidas.map(rec => {
        const img = rec.imagem_url || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80';
        return `
        <div class="bg-creme rounded-3xl overflow-hidden border-2 border-oliva-600/30 hover:border-laranja-500 transition-all flex flex-col justify-between animate-fade-in">
            <div>
                <div class="h-40 w-full relative img-zoom-container">
                    <img src="${esc(img)}" alt="${esc(rec.titulo)}" class="w-full h-full object-cover" loading="lazy">
                    <span class="absolute top-3 left-3 bg-oliva-700 text-white text-[11px] font-extrabold px-3 py-1 rounded-full shadow">
                        ${rec.compatibilidade}% Compatível
                    </span>
                    <span class="absolute bottom-2 right-2 bg-black/75 text-white text-[10px] font-bold px-2.5 py-1 rounded-lg">
                        <i class="fa-solid fa-utensils text-laranja-400 mr-1"></i>${esc(rec.autor)}
                    </span>
                </div>
                <div class="p-5">
                    <h4 class="font-display font-bold text-lg text-grafite mb-2 leading-snug">${esc(rec.titulo)}</h4>
                    <p class="text-xs text-stone-600 mb-2">
                        <strong>Aproveita:</strong> <span class="text-oliva-800 font-semibold">${esc(rec.encontrados.join(', '))}</span>
                    </p>
                    <p class="text-xs text-stone-500 line-clamp-2">${esc(rec.dica_sustentavel)}</p>
                </div>
            </div>
            <div class="px-5 pb-5">
                <button type="button" onclick="abrirModalReceita(${parseInt(rec.id, 10)})"
                        class="w-full bg-laranja-600 hover:bg-laranja-700 text-white text-xs font-bold py-2.5 rounded-xl transition-colors">
                    <i class="fa-solid fa-book-open mr-1"></i> Ver Receita Completa
                </button>
            </div>
        </div>
        `;
    }).join('');
}

function limparGeladeira() {
    document.querySelectorAll('.chk-ingrediente').forEach(cb => cb.checked = false);
    const inputExtra = document.getElementById('input-ingrediente-extra');
    if (inputExtra) inputExtra.value = '';
    atualizarGeladeira();
}

// ===================================================================
// GUIA DE PLANTIO REGIONAL (NUMERAÇÃO SEQUENCIAL E COMPACTA)
// ===================================================================
function filtrarPlantio() {
    const selEstacao = document.getElementById('select-estacao');
    const selEspaco = document.getElementById('select-espaco');
    const grid = document.getElementById('grid-plantio');
    if (!selEstacao || !selEspaco || !grid) return;

    const estacao = selEstacao.value;
    const espaco = selEspaco.value;
    const nomesEstacao = {
        'Primavera': 'Primavera',
        'Verao': 'Verão',
        'Outono': 'Outono',
        'Inverno': 'Inverno'
    };

    const filtrados = CATALOGO_PLANTIO.filter(item =>
        Array.isArray(item.estacoes) && item.estacoes.includes(estacao) &&
        Array.isArray(item.espacos) && item.espacos.includes(espaco)
    );

    grid.innerHTML = filtrados.map((item, idx) => {
        // Remove qualquer número antigo do início ("4. ") e aplica numeração sequencial correta (1, 2, 3...)
        const nomeLimpo = (item.nome || '').replace(/^\d+\.\s*/, '');
        const tituloNumerado = `${idx + 1}. ${nomeLimpo}`;

        return `
        <div class="bg-creme rounded-2xl overflow-hidden border border-stone-200 hover:border-oliva-600 transition-all flex flex-col justify-between animate-fade-in">
            <div>
                <div class="h-32 w-full relative img-zoom-container">
                    <img src="${esc(item.imagem)}" alt="${esc(nomeLimpo)}" class="w-full h-full object-cover" loading="lazy">
                    <span class="absolute top-3 left-3 bg-oliva-700 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full shadow">
                        Ideal no(a) ${esc(nomesEstacao[estacao] || estacao)}
                    </span>
                </div>
                <div class="p-4 pb-2">
                    <h4 class="font-display font-bold text-base text-grafite mb-2">${esc(tituloNumerado)}</h4>
                    <ul class="space-y-1 text-xs text-stone-700 mb-3">
                        <li><i class="fa-solid fa-clock text-laranja-600 w-4"></i> <strong>Colheita:</strong> ${esc(item.colheita)}</li>
                        <li><i class="fa-solid fa-sun text-amber-500 w-4"></i> <strong>Sol:</strong> ${esc(item.sol)}</li>
                        <li><i class="fa-solid fa-droplet text-sky-600 w-4"></i> <strong>Rega:</strong> ${esc(item.rega)}</li>
                    </ul>
                </div>
            </div>
            <div class="px-4 pb-4">
                <div class="bg-white p-2.5 rounded-xl border border-stone-200/80 text-[11px] text-stone-600 leading-relaxed">
                    <strong class="text-oliva-800"><i class="fa-solid fa-location-dot text-laranja-600 mr-1"></i> Dica Vale do Paraíba:</strong> ${esc(item.dicaRegional)}
                </div>
            </div>
        </div>
        `;
    }).join('');
}

let categoriaReceitaAtiva = 'Todas';
const PASSO_RECEITAS = 6; // Exibe exatamente 6 receitas inicialmente
let limiteReceitasVisiveis = PASSO_RECEITAS;

function selecionarCategoriaReceita(categoria) {
    categoriaReceitaAtiva = categoria;
    limiteReceitasVisiveis = PASSO_RECEITAS;

    document.querySelectorAll('.btn-cat-receita').forEach(btn => {
        if (btn.getAttribute('data-cat') === categoria) {
            btn.className = "btn-cat-receita bg-oliva-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-sm";
        } else {
            btn.className = "btn-cat-receita bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold px-4 py-2 rounded-xl transition-all";
        }
    });
    filtrarRepositorioReceitas();
}

function filtrarRepositorioReceitas() {
    const inputBusca = document.getElementById('busca-receitas');
    const termo = inputBusca ? normalizarTexto(inputBusca.value.trim()) : '';
    const cards = Array.from(document.querySelectorAll('.card-receita-item'));

    const cardsFiltrados = cards.filter(card => {
        const titulo = normalizarTexto(card.getAttribute('data-titulo') || '');
        const ing    = normalizarTexto(card.getAttribute('data-ingredientes') || '');
        const cat    = card.getAttribute('data-categoria') || '';

        const bateTexto = (termo === '' || titulo.includes(termo) || ing.includes(termo));
        const bateCat   = (categoriaReceitaAtiva === 'Todas' || cat === categoriaReceitaAtiva);
        return bateTexto && bateCat;
    });

    cards.forEach(card => card.classList.add('hidden'));
    const totalFiltrados = cardsFiltrados.length;
    const qtdExibida = Math.min(limiteReceitasVisiveis, totalFiltrados);

    for (let i = 0; i < qtdExibida; i++) {
        cardsFiltrados[i].classList.remove('hidden');
    }

    const elContador = document.getElementById('contador-receitas-visiveis');
    const btnVerMais = document.getElementById('btn-ver-mais-receitas');
    const textoVerMais = document.getElementById('texto-btn-ver-mais');
    const btnMenos   = document.getElementById('btn-mostrar-menos-receitas');

    if (elContador) {
        elContador.textContent = totalFiltrados > 0
            ? `Exibindo ${qtdExibida} de ${totalFiltrados} receitas sustentáveis`
            : 'Nenhuma receita encontrada para este filtro';
    }

    if (btnVerMais && textoVerMais) {
        const restantes = totalFiltrados - qtdExibida;
        if (restantes > 0) {
            btnVerMais.classList.remove('hidden');
            const proximoLote = Math.min(PASSO_RECEITAS, restantes);
            textoVerMais.textContent = `Ver Mais Receitas (+${proximoLote} de ${restantes} restantes)`;
        } else {
            btnVerMais.classList.add('hidden');
        }
    }

    if (btnMenos) {
        if (qtdExibida > PASSO_RECEITAS) {
            btnMenos.classList.remove('hidden');
        } else {
            btnMenos.classList.add('hidden');
        }
    }
}

function carregarMaisReceitas() {
    limiteReceitasVisiveis += PASSO_RECEITAS;
    filtrarRepositorioReceitas();
}

function recolherReceitas() {
    limiteReceitasVisiveis = PASSO_RECEITAS;
    filtrarRepositorioReceitas();
    const secao = document.getElementById('ferramenta-receitas');
    if (secao) secao.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function abrirModalReceita(id) {
    const rec = RECEITAS_DB.find(r => parseInt(r.id, 10) === parseInt(id, 10));
    if (!rec) return;

    const inputId = document.getElementById('input-comentario-receita-id');
    if (inputId) inputId.value = rec.id;

    const img = rec.imagem_url || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80';
    const itensIngredientes = rec.ingredientes.split(',').map(i =>
        `<li class="flex items-start gap-2"><i class="fa-solid fa-check text-laranja-600 mt-1"></i><span>${esc(i.trim())}</span></li>`
    ).join('');

    document.getElementById('conteudo-modal-receita').innerHTML = `
        <div class="h-56 sm:h-64 w-full rounded-2xl overflow-hidden mb-5 relative">
            <img src="${esc(img)}" alt="${esc(rec.titulo)}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-transparent"></div>
            <div class="absolute bottom-4 left-5 right-5 text-white">
                <span class="inline-block bg-laranja-600 text-white text-[11px] font-extrabold uppercase px-3 py-1 rounded-full mb-2">
                    ${esc(rec.categoria)}
                </span>
                <h3 class="font-display text-2xl sm:text-3xl font-extrabold leading-tight">${esc(rec.titulo)}</h3>
                <p class="text-xs text-stone-200 mt-1"><i class="fa-solid fa-bookmark text-laranja-400 mr-1"></i> Fonte / Autor: <strong>${esc(rec.autor)}</strong></p>
            </div>
        </div>

        <div class="grid sm:grid-cols-12 gap-6 mb-6">
            <div class="sm:col-span-5 bg-creme p-5 rounded-2xl border border-stone-200">
                <h4 class="font-display font-bold text-sm uppercase tracking-wider text-oliva-800 mb-3">
                    <i class="fa-solid fa-basket-shopping mr-1"></i> Ingredientes
                </h4>
                <ul class="space-y-2 text-xs text-stone-700">${itensIngredientes}</ul>
            </div>
            <div class="sm:col-span-7 bg-white p-5 rounded-2xl border border-stone-200">
                <h4 class="font-display font-bold text-sm uppercase tracking-wider text-laranja-700 mb-3">
                    <i class="fa-solid fa-fire-burner mr-1"></i> Modo de Preparo
                </h4>
                <p class="text-xs sm:text-sm text-stone-700 leading-relaxed whitespace-pre-line">${esc(rec.modo_preparo)}</p>
            </div>
        </div>

        <div class="bg-oliva-50 border border-oliva-200 p-4 rounded-2xl text-xs text-oliva-900">
            <strong><i class="fa-solid fa-seedling text-laranja-600 mr-1"></i> Impacto Sustentável:</strong> ${esc(rec.dica_sustentavel)}
        </div>
    `;

    const divCom = document.getElementById('lista-comentarios-modal-receita');
    if (!rec.comentarios || rec.comentarios.length === 0) {
        divCom.innerHTML = `<p class="text-xs text-stone-500 italic bg-stone-50 p-3.5 rounded-xl">Nenhuma avaliação publicada ainda para esta receita. Seja o primeiro a avaliar abaixo!</p>`;
    } else {
        divCom.innerHTML = rec.comentarios.map(c => {
            const notaNum = Math.min(Math.max(parseInt(c.nota, 10) || 5, 1), 5);
            return `
            <div class="bg-stone-50 p-4 rounded-2xl border border-stone-200">
                <div class="flex items-center justify-between mb-1">
                    <strong class="text-xs text-grafite">${esc(c.nome_usuario)}</strong>
                    <span class="text-amber-500 text-xs">${'★'.repeat(notaNum)}${'☆'.repeat(5 - notaNum)}</span>
                </div>
                <p class="text-xs text-stone-600">${esc(c.texto)}</p>
            </div>
            `;
        }).join('');
    }

    document.getElementById('modal-receita-detalhe').classList.remove('hidden');
}

function abrirModalNovaReceita() {
    document.getElementById('modal-nova-receita').classList.remove('hidden');
}

// ===================================================================
// MAPA E GUIA DE SUSTENTABILIDADE NO VALE E TAUBATÉ
// ===================================================================
function atualizarMapaIframe(queryBusca, tituloLocal) {
    const iframe = document.getElementById('iframe-mapa-taubate');
    const titulo = document.getElementById('titulo-mapa-ativo');
    if (!iframe || !titulo) return;

    iframe.src = `https://maps.google.com/maps?q=${encodeURIComponent(queryBusca)}&t=&z=15&ie=UTF8&iwloc=&output=embed`;
    titulo.textContent = tituloLocal;
}

function filtrarLocaisMapa() {
    const selCidade = document.getElementById('filtro-cidade-mapa');
    const selTipo   = document.getElementById('filtro-tipo-mapa');
    const cidade = selCidade ? selCidade.value : 'Todas';
    const tipo   = selTipo ? selTipo.value : 'Todos';

    document.querySelectorAll('.card-local-item').forEach(card => {
        const cardCidade = card.getAttribute('data-cidade') || '';
        const cardTipo   = card.getAttribute('data-tipo') || '';

        const bateCidade = (cidade === 'Todas' || cardCidade === cidade);
        const bateTipo   = (tipo === 'Todos' || cardTipo.toLowerCase().includes(tipo.toLowerCase()));

        if (bateCidade && bateTipo) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

function abrirModalLocalComentarios(id) {
    const loc = LOCAIS_DB.find(l => parseInt(l.id, 10) === parseInt(id, 10));
    if (!loc) return;

    const inputId = document.getElementById('input-comentario-local-id');
    if (inputId) inputId.value = loc.id;

    document.getElementById('cabecalho-modal-local').innerHTML = `
        <span class="text-xs font-extrabold uppercase text-laranja-600">${esc(loc.tipo)} • ${esc(loc.cidade)} - SP</span>
        <h3 class="font-display text-2xl font-extrabold text-grafite mt-0.5">${esc(loc.nome)}</h3>
        <p class="text-xs text-stone-500 mt-1"><i class="fa-solid fa-location-dot text-laranja-600 mr-1"></i> ${esc(loc.endereco)}</p>
    `;

    const divCom = document.getElementById('lista-comentarios-modal-local');
    if (!loc.comentarios || loc.comentarios.length === 0) {
        divCom.innerHTML = `<p class="text-xs text-stone-500 italic bg-stone-50 p-3.5 rounded-xl">Ainda não há avaliações aprovadas para este local. Envie sua avaliação abaixo!</p>`;
    } else {
        divCom.innerHTML = loc.comentarios.map(c => {
            const notaNum = Math.min(Math.max(parseInt(c.nota, 10) || 5, 1), 5);
            return `
            <div class="bg-stone-50 p-4 rounded-2xl border border-stone-200">
                <div class="flex items-center justify-between mb-1">
                    <strong class="text-xs text-grafite">${esc(c.nome_usuario)}</strong>
                    <span class="text-amber-500 text-xs">${'★'.repeat(notaNum)}${'☆'.repeat(5 - notaNum)}</span>
                </div>
                <p class="text-xs text-stone-600">${esc(c.texto)}</p>
            </div>
            `;
        }).join('');
    }

    document.getElementById('modal-local-comentarios').classList.remove('hidden');
}

function fecharModal(idModal) {
    const modal = document.getElementById(idModal);
    if (modal) modal.classList.add('hidden');
}

// ===================================================================
// INICIALIZAÇÃO AUTOMÁTICA NO DOMCONTENTLOADED
// ===================================================================
document.addEventListener('DOMContentLoaded', () => {
    inicializarMenuMobile();
    sortearDezMitos();
    renderizarMitos();
    

    // Pré-seleciona 3 ingredientes variados na Geladeira Inteligente
    const chks = document.querySelectorAll('.chk-ingrediente');
    if (chks.length >= 12) {
        chks[0].checked = true;  // Arroz cozido
        chks[3].checked = true;  // Casca de Banana
        chks[11].checked = true; // Batata
    }
    atualizarGeladeira();
    filtrarPlantio();
    filtrarRepositorioReceitas();
});