function delete_confirm(question,pecaID) {
    if (confirm(question)) {
        location.href = 'scripts/tabelaPecasDelete/delete-peca.php?pecaID='+pecaID;
    } else {
        return false;
    }
}