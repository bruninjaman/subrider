function delete_confirm(question,ordemID,Codigo) {
    if (confirm(question)) {
        location.href = 'scripts/tabelaOrdensDelete/delete-service.php?ordemID='+ordemID+'&Codigo='+Codigo;
    } else {
        return false;
    }
}