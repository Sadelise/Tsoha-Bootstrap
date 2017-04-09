<?php

class PlantController extends BaseController {

    public static function index() {
        $plants = Plant::all();
        View::make('suunnitelmat/list_plant.html', array('plants' => $plants));
    }

    public static function show($id) {
        $plant = Plant::find($id);
        View::make('suunnitelmat/plantdescription.html', array('plant' => $plant));
    }

    public static function edit($id) {
        $plant = Plant::find($id);
        View::make('suunnitelmat/edit_plant.html', array('plant' => $plant));
    }

    public static function newPlant() {
        View::make('suunnitelmat/addPlant.html');
    }

    public static function store() {
        $params = $_POST;
        $attributes = array(
            'tradename' => $params['tradename'],
            'latin_name' => $params['latin_name'],
            'light' => $params['light'],
            'water' => $params['water'],
            'description' => $params['description']
        );

        $plant = new Plant($attributes);
        $errors = $plant->errors();

        if (count($errors) == 0) {
            $plant->save();
            Redirect::to('/description/' . $plant->id, array('message' => 'Kasvi tallennettu!'));
        } else {
            View::make('suunnitelmat/addPlant.html', array('errors' => $errors, 'attributes' => $attributes));
        }
    }

    public static function update($id) {
        $params = $_POST;

        $attributes = array(
            'id' => $id,
            'tradename' => $params['tradename'],
            'latin_name' => $params['latin_name'],
            'light' => $params['light'],
            'water' => $params['water'],
            'description' => $params['description']
        );

        $plant = new Plant($attributes);
        $errors = $plant->errors();

        if (count($errors) > 0) {
            View::make('suunnitelmat/edit_plant.html', array('errors' => $errors, 'attributes' => $attributes));
        } else {
            $plant->update();

            Redirect::to('/description/' . $plant->id, array('message' => 'Kasvia on muokattu onnistuneesti!'));
        }
    }

    public static function destroy($id) {
        $plant = new Plant(array('id' => $id));

        $writers = Writer::findAllByPlant($id);
        if ($writers) {
            $writer = new Writer(array('plant_id' => $writers['plant_id']));
            $writer->destroy();
        }

        $owned_plants = OwnedPlant::allByPlantId($id);
        if ($owned_plants) {
            $owned_plant = new OwnedPlant(array('id' => $owned_plants['id']));
            $diary_entries = Diary::all();
            if ($diary_entries) {
                $diary = new Diary(array('id' => $diary_entries['id']));
                $diary->destroy();
            }
            $owned_plant->destroy();
        }

        $plant->destroy();

        Redirect::to('/list_p', array('message' => 'Kasvi on poistettu onnistuneesti!'));
    }

}
