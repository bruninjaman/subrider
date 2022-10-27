function delete_confirm(question,servID) {
    if (confirm(question)) {
        location.href = 'scripts/tabelaMotosDelete/delete-serv.php?servID='+servID;
    } else {
        return false;
    }
}