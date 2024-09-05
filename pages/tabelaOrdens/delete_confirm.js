function delete_confirm(question,motoID) {
    if (confirm(question)) {
        location.href = 'scripts/tabelaMotos/delete-moto.php?motoID='+motoID;
    } else {
        return false;
    }
}