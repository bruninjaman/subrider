function delete_confirm(question,ordemID,Ordem) {
    if (confirm(question)) {
        location.href = 'scripts/tabelaOrdensDelete/delete-service.php?ordemID='+ordemID+'&Ordem='+Ordem;
    } else {
        return false;
    }
}