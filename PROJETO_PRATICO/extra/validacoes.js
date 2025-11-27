function validarUsuario() {
    const nomeEl   = document.getElementById("nome");
    const emailEl  = document.getElementById("email");
    const senhaEl  = document.getElementById("senha");
    const perfilEl = document.getElementById("id_perfil");

    const nome   = (nomeEl?.value || "").trim();
    const email  = (emailEl?.value || "").trim();
    const senha  = (senhaEl?.value || "");
    const perfil = (perfilEl?.value || "").trim();

    // Nome: mínimo 3, apenas letras/acentos/apóstrofo e espaços
    const nomeRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ'\s]{3,}$/;
    if (!nomeRegex.test(nome)) {
        alert(" Nome inválido. Use apenas letras (com acentos), espaços e apóstrofo, com pelo menos 3 caracteres.");
        nomeEl.focus();
        return false;
    }

    // E-mail
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    if (!emailRegex.test(email)) {
        alert(" Informe um e-mail válido (ex: usuario@dominio.com).");
        emailEl.focus();
        return false;
    }

    // Senha
    if (senha.length < 6) {
        alert(" A senha deve ter no mínimo 6 caracteres.");
        senhaEl.focus();
        return false;
    }
    if (/\s/.test(senha)) {
        alert(" A senha não pode conter espaços.");
        senhaEl.focus();
        return false;
    }
    if (!/[A-Za-z]/.test(senha) || !/\d/.test(senha)) {
        alert(" A senha deve conter ao menos uma letra e um número.");
        senhaEl.focus();
        return false;
    }

    // Perfil
    if (!["1", "2", "3", "4"].includes(perfil)) {
        alert(" Selecione um perfil válido.");
        perfilEl.focus();
        return false;
    }

    return true;
}

function validarBusca() {
    const busca = document.getElementById("busca").value.trim();

    if (busca === "") {
        return confirm("Nenhum termo informado. Deseja listar todos os usuários?");
    }

    const numeroRegex = /^[0-9]+$/;
    const nomeRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ'\s]+$/; // mesmo do cadastrar/alterar

    if (!(numeroRegex.test(busca) || nomeRegex.test(busca))) {
        alert(" Digite apenas números (ID) ou letras (Nome).");
        document.getElementById("busca").focus();
        return false;
    }

    return true;
}

function validarAlterar() {
    const nomeEl   = document.getElementById("nome");
    const emailEl  = document.getElementById("email");
    const perfilEl = document.getElementById("perfil");

    const nome   = nomeEl.value.trim();
    const email  = emailEl.value.trim();
    const perfil = perfilEl.value.trim();

    // Nome igual ao do cadastrar
    const nomeRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ'\s]{3,}$/;
    if (!nomeRegex.test(nome)) {
        alert(" Nome inválido! Digite apenas letras, com no mínimo 3 caracteres.");
        nomeEl.focus();
        return false;
    }

    // E-mail igual ao do cadastrar
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    if (!emailRegex.test(email)) {
        alert(" Email inválido!");
        emailEl.focus();
        return false;
    }

    // Perfil: precisa ser número válido
    if (perfil === "" || isNaN(perfil)) {
        alert(" Perfil inválido!");
        perfilEl.focus();
        return false;
    }

    return true;
}
