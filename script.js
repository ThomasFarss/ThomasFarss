const STORAGE_KEY = "myfinance-dashboard-v2";
const DEFAULT_LOGO = "assets/myfinance-logo.svg";
const MONTHS = ["JAN", "FEV", "MAR", "ABR", "MAI", "JUN", "JUL", "AGO", "SET", "OUT", "NOV", "DEZ"];

const now = new Date();
const state = {
  logo: DEFAULT_LOGO,
  entries: [],
  loans: [],
  currentMonth: now.getMonth(),
  currentYear: now.getFullYear(),
  historyYear: now.getFullYear()
};

const $ = (id) => document.getElementById(id);
const brl = (v) => Number(v || 0).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });

function save() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
}

function load() {
  const raw = localStorage.getItem(STORAGE_KEY);
  if (!raw) return;
  try {
    const parsed = JSON.parse(raw);
    Object.assign(state, {
      logo: parsed.logo || DEFAULT_LOGO,
      entries: Array.isArray(parsed.entries) ? parsed.entries : [],
      loans: Array.isArray(parsed.loans) ? parsed.loans : [],
      currentMonth: Number.isInteger(parsed.currentMonth) ? parsed.currentMonth : now.getMonth(),
      currentYear: Number.isInteger(parsed.currentYear) ? parsed.currentYear : now.getFullYear(),
      historyYear: Number.isInteger(parsed.historyYear) ? parsed.historyYear : now.getFullYear()
    });
  } catch {
    console.warn("Falha ao carregar dados salvos.");
  }
}

function monthEntries(month = state.currentMonth, year = state.currentYear) {
  return state.entries.filter((e) => e.month === month && e.year === year);
}

function totals(list = monthEntries()) {
  const receita = list.filter((e) => e.tipo === "entrada").reduce((a, b) => a + b.valor, 0);
  const fixa = list.filter((e) => e.tipo === "fixa").reduce((a, b) => a + b.valor, 0);
  const variavel = list.filter((e) => e.tipo === "variavel").reduce((a, b) => a + b.valor, 0);
  const cartao = list.filter((e) => e.tipo === "cartao").reduce((a, b) => a + b.valor, 0);
  const emprestimoMes = state.loans.reduce((acc, l) => (l.pagas < l.total ? acc + l.valorParcela : acc), 0);
  const despesas = fixa + variavel;
  const totalPago = despesas;
  const totalPagar = cartao + emprestimoMes;
  const saldo = receita - despesas - cartao - emprestimoMes;
  return { receita, fixa, variavel, despesas, cartao, emprestimoMes, totalPago, totalPagar, saldo };
}

function renderTopMeta() {
  $("logoPreview").src = state.logo || DEFAULT_LOGO;
  $("todayLabel").textContent = new Date().toLocaleDateString("pt-BR", { weekday: "long", day: "2-digit", month: "long", year: "numeric" });
  $("yearLabel").textContent = state.currentYear;
  $("monthLabel").textContent = MONTHS[state.currentMonth];
  $("reportMonthLabel").textContent = `${MONTHS[state.currentMonth]} ${state.currentYear}`;

  $("monthList").innerHTML = MONTHS.map((m, i) => `<button class="${i === state.currentMonth ? "active" : ""}" onclick="setMonth(${i})">${m}</button>`).join("");
}

function renderKpis() {
  const t = totals();
  const cards = [
    ["Total Pago", t.totalPago, "money-blue"],
    ["Total a Pagar", t.totalPagar, "money-orange"],
    ["Total Vencido", 0, "money-red"],
    ["Total Receitas", t.receita, "money-green"],
    ["Saldo Disponível", t.saldo, t.saldo >= 0 ? "money-green" : "money-red"]
  ];
  $("kpiRow").innerHTML = cards
    .map(([title, value, klass]) => `<article class="kpi"><h4>${title}</h4><strong class="${klass}">${brl(value)}</strong></article>`)
    .join("");

  $("fixasValor").textContent = brl(t.fixa);
  $("variaveisValor").textContent = brl(t.variavel);
  $("cartaoValor").textContent = brl(t.cartao);
  $("emprestimoMensal").textContent = brl(t.emprestimoMes);

  $("saldoHero").textContent = brl(t.saldo);
  $("barReceitas").textContent = brl(t.receita);
  $("barDespesas").textContent = brl(t.despesas);
  $("barCartao").textContent = brl(t.cartao);
  $("barEmprestimo").textContent = brl(t.emprestimoMes);
}

function renderMainLists() {
  const list = monthEntries();
  const render = (id, predicate, emptyText) => {
    const filtered = list.filter(predicate);
    $(id).innerHTML = filtered.length
      ? filtered
          .map(
            (item, i) => `<li><div><strong>${item.desc}</strong><br/><small>${MONTHS[item.month]} ${item.year}</small></div><strong>${brl(item.valor)}</strong><button onclick="removeEntry('${item.id}')">Excluir</button></li>`
          )
          .join("")
      : `<li><small>${emptyText}</small></li>`;
  };

  render("despesasList", (e) => e.tipo === "fixa" || e.tipo === "variavel", "Nenhuma despesa neste mês");
  render("receitasList", (e) => e.tipo === "entrada", "Nenhuma receita neste mês");
  render("cartaoList", (e) => e.tipo === "cartao", "Nenhum lançamento de cartão neste mês");

  $("loanList").innerHTML = state.loans.length
    ? state.loans
        .map((l) => {
          const faltam = Math.max(l.total - l.pagas, 0);
          return `<li><div><strong>${l.nome}</strong><br/><small>${l.credor} • ${l.pagas}/${l.total} pagas • faltam ${faltam}</small></div><strong>${brl(l.valorParcela)}</strong><div><button onclick="payLoan('${l.id}')">+1</button> <button onclick="removeLoan('${l.id}')">Excluir</button></div></li>`;
        })
        .join("")
    : "<li><small>Nenhum empréstimo neste mês</small></li>";
}

function renderRelatorio() {
  const t = totals();
  const rendaComp = t.receita > 0 ? ((t.totalPagar + t.despesas) / t.receita) * 100 : 0;
  const rows = [
    ["Total de Receitas", t.receita, "money-green"],
    ["Despesas Fixas", t.fixa, "money-blue"],
    ["Despesas Variáveis", t.variavel, "money-orange"],
    ["Fatura do Cartão", t.cartao, "money-blue"],
    ["Parcelas Empréstimos", t.emprestimoMes, "money-purple"],
    ["Total Pago", t.totalPago, "money-blue"],
    ["Total a Pagar", t.totalPagar, "money-orange"],
    ["Total Vencido", 0, "money-red"],
    ["Comprometimento Renda", `${rendaComp.toFixed(1)}%`, "money-teal"],
    ["Saldo Disponível", t.saldo, t.saldo >= 0 ? "money-green" : "money-red"]
  ];

  $("reportSummary").innerHTML = rows
    .map(([k, v, cls]) => `<li><span>${k}</span><strong class="${cls || ""}">${typeof v === "string" ? v : brl(v)}</strong></li>`)
    .join("");

  $("reportLoans").innerHTML = state.loans.length
    ? state.loans
        .map((l) => `<li><span>${l.nome}</span><strong>${l.pagas}/${l.total} • faltam ${Math.max(l.total - l.pagas, 0)}</strong></li>`)
        .join("")
    : "<li><span>Nenhum empréstimo cadastrado</span><strong>—</strong></li>";

  const limite = t.receita * 0.4;
  const disponivelCartao = Math.max(limite - t.cartao, 0);
  $("reportCard").innerHTML = [
    ["Cartão", "•••• •••• 9922"],
    ["Limite Total", brl(limite)],
    ["Disponível", brl(disponivelCartao)],
    ["Vencimento", "Dia 10"],
    ["Uso do Limite", limite > 0 ? `${((t.cartao / limite) * 100).toFixed(1)}%` : "0%"]
  ]
    .map(([a, b]) => `<li><span>${a}</span><strong>${b}</strong></li>`)
    .join("");

  const tip =
    t.saldo >= 0
      ? `Excelente! Apenas ${rendaComp.toFixed(1)}% da renda comprometida. Continue assim!`
      : `Atenção: saldo negativo em ${brl(Math.abs(t.saldo))}. Reduza despesas variáveis e fatura do cartão.`;
  $("reportTips").textContent = tip;
}

function renderHistorico() {
  $("historyYear").textContent = state.historyYear;
  let acc = { receita: 0, despesa: 0, cartao: 0, emp: 0, saldo: 0 };

  $("historyBody").innerHTML = MONTHS.map((month, i) => {
    const list = monthEntries(i, state.historyYear);
    const receita = list.filter((e) => e.tipo === "entrada").reduce((a, b) => a + b.valor, 0);
    const despesa = list.filter((e) => e.tipo === "fixa" || e.tipo === "variavel").reduce((a, b) => a + b.valor, 0);
    const cartao = list.filter((e) => e.tipo === "cartao").reduce((a, b) => a + b.valor, 0);
    const emp = state.loans.reduce((sum, l) => {
      if (l.pagas < l.total && l.startYear <= state.historyYear && l.startMonth <= i) return sum + l.valorParcela;
      return sum;
    }, 0);
    const saldo = receita - despesa - cartao - emp;
    acc.receita += receita;
    acc.despesa += despesa;
    acc.cartao += cartao;
    acc.emp += emp;
    acc.saldo += saldo;

    return `<tr><td>${month} ${state.historyYear}</td><td class="money-green">${brl(receita)}</td><td class="money-red">${brl(despesa)}</td><td class="money-blue">${brl(cartao)}</td><td class="money-purple">${brl(emp)}</td><td class="${saldo >= 0 ? "money-green" : "money-red"}">${brl(saldo)}</td></tr>`;
  }).join("");

  $("historyFoot").innerHTML = `<tr><td>TOTAL ${state.historyYear}</td><td class="money-green">${brl(acc.receita)}</td><td class="money-red">${brl(acc.despesa)}</td><td class="money-blue">${brl(acc.cartao)}</td><td class="money-purple">${brl(acc.emp)}</td><td class="${acc.saldo >= 0 ? "money-green" : "money-red"}">${brl(acc.saldo)}</td></tr>`;
}

function rerender() {
  renderTopMeta();
  renderKpis();
  renderMainLists();
  renderRelatorio();
  renderHistorico();
  save();
}

window.setMonth = (m) => {
  state.currentMonth = m;
  rerender();
};
window.removeEntry = (id) => {
  state.entries = state.entries.filter((e) => e.id !== id);
  rerender();
};
window.removeLoan = (id) => {
  state.loans = state.loans.filter((l) => l.id !== id);
  rerender();
};
window.payLoan = (id) => {
  const loan = state.loans.find((l) => l.id === id);
  if (!loan || loan.pagas >= loan.total) return;
  loan.pagas += 1;
  rerender();
};

function openEntryDialog(tipo) {
  const dialog = $("entryDialog");
  $("entryTipo").value = tipo;
  $("dialogTitle").textContent = `Novo lançamento (${tipo})`;
  dialog.showModal();
}

function bindEvents() {
  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".tab-btn").forEach((b) => b.classList.remove("active"));
      document.querySelectorAll(".tab-content").forEach((t) => t.classList.remove("active"));
      btn.classList.add("active");
      $(`tab-${btn.dataset.tab}`).classList.add("active");
    });
  });

  $("prevMonth").addEventListener("click", () => {
    state.currentMonth -= 1;
    if (state.currentMonth < 0) {
      state.currentMonth = 11;
      state.currentYear -= 1;
    }
    rerender();
  });
  $("nextMonth").addEventListener("click", () => {
    state.currentMonth += 1;
    if (state.currentMonth > 11) {
      state.currentMonth = 0;
      state.currentYear += 1;
    }
    rerender();
  });

  $("prevYear").addEventListener("click", () => {
    state.historyYear -= 1;
    rerender();
  });
  $("nextYear").addEventListener("click", () => {
    state.historyYear += 1;
    rerender();
  });

  $("quickDespesa").addEventListener("click", () => openEntryDialog("variavel"));
  $("quickReceita").addEventListener("click", () => openEntryDialog("entrada"));
  $("quickCartao").addEventListener("click", () => openEntryDialog("cartao"));
  $("quickLoan").addEventListener("click", () => $("loanDialog").showModal());

  $("entryForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const desc = $("entryDesc").value.trim();
    const valor = Number($("entryValor").value);
    const tipo = $("entryTipo").value;
    if (!desc || valor <= 0) return;

    state.entries.unshift({
      id: crypto.randomUUID(),
      desc,
      valor,
      tipo,
      month: state.currentMonth,
      year: state.currentYear,
      createdAt: Date.now()
    });

    e.target.reset();
    $("entryDialog").close();
    rerender();
  });

  $("loanForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const nome = $("loanNome").value.trim();
    const credor = $("loanCredor").value.trim();
    const total = Number($("loanTotalParc").value);
    const pagas = Number($("loanPagas").value);
    const valorParcela = Number($("loanValorParc").value);

    if (!nome || !credor || total <= 0 || pagas < 0 || pagas > total || valorParcela <= 0) return;

    state.loans.unshift({
      id: crypto.randomUUID(),
      nome,
      credor,
      total,
      pagas,
      valorParcela,
      startMonth: state.currentMonth,
      startYear: state.currentYear
    });

    e.target.reset();
    $("loanDialog").close();
    rerender();
  });

  $("logoInput").addEventListener("change", (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = () => {
      state.logo = reader.result;
      rerender();
    };
    reader.readAsDataURL(file);
  });

  $("resetLogoBtn").addEventListener("click", () => {
    state.logo = DEFAULT_LOGO;
    rerender();
  });

  $("saveBtn").addEventListener("click", () => {
    save();
    alert("Dados salvos localmente com sucesso!");
  });

  $("exportBtn").addEventListener("click", () => {
    const blob = new Blob([JSON.stringify(state, null, 2)], { type: "application/json" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `myfinance-backup-${new Date().toISOString().slice(0, 10)}.json`;
    a.click();
    URL.revokeObjectURL(url);
  });

  $("importInput").addEventListener("change", (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = () => {
      try {
        const data = JSON.parse(String(reader.result));
        state.logo = data.logo || DEFAULT_LOGO;
        state.entries = Array.isArray(data.entries) ? data.entries : [];
        state.loans = Array.isArray(data.loans) ? data.loans : [];
        state.currentMonth = Number.isInteger(data.currentMonth) ? data.currentMonth : now.getMonth();
        state.currentYear = Number.isInteger(data.currentYear) ? data.currentYear : now.getFullYear();
        state.historyYear = Number.isInteger(data.historyYear) ? data.historyYear : state.currentYear;
        rerender();
      } catch {
        alert("Arquivo de backup inválido.");
      }
    };
    reader.readAsText(file);
  });

  $("clearBtn").addEventListener("click", () => {
    if (!confirm("Deseja apagar todos os dados?")) return;
    state.logo = DEFAULT_LOGO;
    state.entries = [];
    state.loans = [];
    state.currentMonth = now.getMonth();
    state.currentYear = now.getFullYear();
    state.historyYear = now.getFullYear();
    rerender();
  });
}

load();
bindEvents();
rerender();
