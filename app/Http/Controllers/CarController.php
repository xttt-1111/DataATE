<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;

class CarController extends Controller
{
    /**
     * Display a listing of cars.
     */
    public function index()
    {
        $cars = Car::all();
        return response()->json($cars);
    }

    /**
     * Store a newly created car in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'plate_no' => 'required|string|unique:car,plate_no',
            'model' => 'required|string|max:255',
            'price_hour' => 'required|numeric',
            'availability_status' => 'required|boolean',
            'fuel_level' => 'required|numeric|min:0|max:100',
            'car_mileage' => 'required|numeric',
        ]);

        $car = Car::create($validatedData);

        return response()->json([
            'message' => 'Car created successfully',
            'car' => $car
        ], 201);
    }

    /**
     * Display the specified car.
     */
    public function show($plate_no)
    {
        $car = Car::find($plate_no);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        return response()->json($car);
    }

    /**
     * Update the specified car in storage.
     */
    public function update(Request $request, $plate_no)
    {
        $car = Car::find($plate_no);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $validatedData = $request->validate([
            'model' => 'sometimes|required|string|max:255',
            'price_hour' => 'sometimes|required|numeric',
            'availability_status' => 'sometimes|required|boolean',
            'fuel_level' => 'sometimes|required|numeric|min:0|max:100',
            'car_mileage' => 'sometimes|required|numeric',
        ]);

        $car->update($validatedData);

        return response()->json([
            'message' => 'Car updated successfully',
            'car' => $car
        ]);
    }

    /**
     * Remove the specified car from storage.
     */
    public function destroy($plate_no)
    {
        $car = Car::find($plate_no);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $car->delete();

        return response()->json(['message' => 'Car deleted successfully']);
    }
}
