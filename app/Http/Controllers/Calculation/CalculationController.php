<?php

/**
 * Copyright (C) 2017 Bynq.io B.V.
 * All Rights Reserved
 *
 * This file is part of the Dynq project.
 *
 * Content can not be copied and/or distributed without the express
 * permission of the author.
 *
 * @package  Dynq
 * @author   Yorick de Wid <y.dewid@calculatietool.com>
 */

namespace BynqIO\Dynq\Http\Controllers\Calculation;

use \Illuminate\Http\Request;
use BynqIO\Dynq\Models\Project;
use BynqIO\Dynq\Models\Chapter;
use BynqIO\Dynq\Models\Part;
use BynqIO\Dynq\Models\PartType;
use BynqIO\Dynq\Models\ProjectType;
use BynqIO\Dynq\Models\Tax;
use BynqIO\Dynq\Models\Activity;
use BynqIO\Dynq\Models\FavoriteActivity;
use BynqIO\Dynq\Calculus\InvoiceTerm;
use BynqIO\Dynq\Calculus\ResultEndresult;
use BynqIO\Dynq\Calculus\CalculationRegister;
use BynqIO\Dynq\Models\CalculationMaterial;
use BynqIO\Dynq\Models\CalculationEquipment;
use BynqIO\Dynq\Models\CalculationLabor;
use BynqIO\Dynq\Models\FavoriteLabor;
use BynqIO\Dynq\Models\FavoriteMaterial;
use BynqIO\Dynq\Models\FavoriteEquipment;
use BynqIO\Dynq\Models\EstimateLabor;
use BynqIO\Dynq\Models\EstimateMaterial;
use BynqIO\Dynq\Models\EstimateEquipment;
use BynqIO\Dynq\Models\Invoice;
use BynqIO\Dynq\Models\Offer;
use BynqIO\Dynq\Http\Controllers\Controller;

use \Auth;
use \PDF;

class CalculationController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Default Home Controlluse BynqIO\Dynq\Models\Invoice;er
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |	Route::get('/', 'HomeController@showWelcome');
    |
    */

    protected function newLaborRow($activity, Array $parameters)
    {
        return CalculationLabor::create([
            "rate"            => $rate,
            "amount"          => $parameters['amount'],
            "activity_id"     => $activity->id,
        ]);
    }

    protected function newMaterialRow($activity, Array $parameters)
    {
        return CalculationMaterial::create([
            "material_name"   => $parameters['name'],
            "unit"            => $parameters['unit'],
            "rate"            => $parameters['rate'],
            "amount"          => $parameters['amount'],
            "activity_id"     => $activity->id,
        ]);
    }

    protected function newOtherRow($activity, Array $parameters)
    {
        return CalculationEquipment::create([
            "equipment_name" => $parameters['name'],
            "unit"           => $parameters['unit'],
            "rate"           => $parameters['rate'],
            "amount"         => $parameters['amount'],
            "activity_id"    => $activity->id,
        ]);
    }

    public function new(Request $request)
    {
        $this->validate($request, [
            'name'      => ['required_unless:layer,labor', 'max:100'],
            'unit'      => ['required_unless:layer,labor', 'max:10'],
            'rate'      => ['required', 'numeric'],
            'amount'    => ['required', 'numeric'],
            'activity'  => ['required', 'integer', 'min:0'],
            'layer'     => ['required'],
        ]);

        $id = null;

        $activity = Activity::findOrFail($request->get('activity'));

        switch ($request->get('layer')) {
            case 'labor':
                $id = $this->newLaborRow($activity, $request->all())->id;
                break;
            case 'material':
                $id = $this->newMaterialRow($activity, $request->all())->id;
                break;
            case 'other':
                $id = $this->newOtherRow($activity, $request->all())->id;
                break;
        }

        return response()->json(['success' => 1, 'id' => $id]);
    }

    protected function updateLaborRow(Array $parameters)
    {
        $row = CalculationLabor::findOrFail($parameters['id']);
        $row->rate           = $parameters['rate'];
        $row->amount         = $parameters['amount'];
        $row->save();
    }

    protected function updateMaterialRow(Array $parameters)
    {
        $row = CalculationMaterial::findOrFail($parameters['id']);
        $row->material_name  = $parameters['name'];
        $row->unit           = $parameters['unit'];
        $row->rate           = $parameters['rate'];
        $row->amount         = $parameters['amount'];
        $row->save();
    }

    protected function updateOtherRow(Array $parameters)
    {
        $row = CalculationEquipment::findOrFail($parameters['id']);
        $row->equipment_name  = $parameters['name'];
        $row->unit            = $parameters['unit'];
        $row->rate            = $parameters['rate'];
        $row->amount          = $parameters['amount'];
        $row->save();
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id'        => ['required', 'integer', 'min:0'],
            'name'      => ['required_unless:layer,labor', 'max:100'],
            'unit'      => ['required_unless:layer,labor', 'max:10'],
            'rate'      => ['required', 'numeric'],
            'amount'    => ['required', 'numeric'],
            'layer'     => ['required'],
        ]);

        switch ($request->get('layer')) {
            case 'labor':
                $this->updateLaborRow($request->all());
                break;
            case 'material':
                $this->updateMaterialRow($request->all());
                break;
            case 'other':
                $this->updateOtherRow($request->all());
                break;
        }

        return response()->json(['success' => 1]);
    }

    protected function deleteLaborRow(Array $parameters)
    {
        CalculationLabor::findOrFail($parameters['id'])->delete();
    }

    protected function deleteMaterialRow(Array $parameters)
    {
        $row = CalculationMaterial::findOrFail($parameters['id'])->delete();
    }

    protected function deleteOtherRow(Array $parameters)
    {
        $row = CalculationEquipment::findOrFail($parameters['id'])->delete();
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id'        => ['required', 'integer', 'min:0'],
            'activity'  => ['required', 'integer', 'min:0'],
            'layer'     => ['required'],
        ]);

        switch ($request->get('layer')) {
            case 'labor':
                $id = $this->deleteLaborRow($request->all());
                break;
            case 'material':
                $id = $this->deleteMaterialRow($request->all());
                break;
            case 'other':
                $id = $this->deleteOtherRow($request->all());
                break;
        }

        return response()->json(['success' => 1]);
    }

}
