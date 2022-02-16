<tr>
    <td>
        <span class="custom-checkbox">
            <input type="checkbox" id="checkbox1" name="options[]" value="1">
            <label for="checkbox1"></label>
        </span>
    </td>
    <!--<td><img src="https://static.thenounproject.com/png/3674270-200.png" style="height:100px;width:100px;"></td> -->
    <td class="rowfoto"><img src="<?php echo $row["foto"] ?>" style="height:100px;width:100px;"></td>
    <td class="rowproprietario"><?php echo $row["proprietario"]?></td>
    <td class="rowendereco"><?php echo $row["endereco"]?></td>
    <td class="rowmarca"><?php echo $row["marca"]?></td>
    <td class="rowplaca"><?php echo $row["placa"]?></td>
    <td class="rowano"><?php echo $row["ano"]?></td>
    <td class="rowmodelo"><?php echo $row["modelo"]?></td>
    <td class="rowkm"><?php echo $row["km"]?></td>
    <td class="hidden motoid"><?php echo $row["motoID"]?></td>
    <td>
        <a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
        <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
    </td>
</tr>