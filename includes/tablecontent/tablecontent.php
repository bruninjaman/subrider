<tr>
    <td>
        <span class="custom-checkbox">
            <input type="checkbox" id="checkbox1" name="options[]" value="1">
            <label for="checkbox1"></label>
        </span>
    </td>
    <!--<td><img src="https://static.thenounproject.com/png/3674270-200.png" style="height:100px;width:100px;"></td> -->
    <td><img src="<?php echo $row["foto"] ?>" style="height:100px;width:100px;"></td>
    <td><?php echo $row["proprietario"]?></td>
    <td><?php echo $row["endereco"]?></td>
    <td><?php echo $row["marca"]?></td>
    <td><?php echo $row["placa"]?></td>
    <td><?php echo $row["ano"]?></td>
    <td><?php echo $row["modelo"]?></td>
    <td><?php echo $row["km"]?></td>
    <td>
        <a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
        <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
    </td>
</tr>