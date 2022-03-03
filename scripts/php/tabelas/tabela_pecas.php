<tr>
    <td>
        <span class="custom-checkbox">
            <input type="checkbox" id="checkbox1" name="options[]" value="1">
            <label for="checkbox1"></label>
        </span>
    </td>
    <td class="rowfoto"><img src="<?php echo $row["foto"] ?>" style="height:100px;width:100px;"></td>
    <td class="rowgrupo"><?php echo $row["grupo"]?></td>
    <td class="rowitem"><?php echo $row["item"]?></td>
    <td class="rowparte"><?php echo $row["parte"]?></td>
    <td class="hidden pecaId"><?php echo $row["pecaId"]?></td>
    <td>
        <a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
        <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
    </td>
</tr>