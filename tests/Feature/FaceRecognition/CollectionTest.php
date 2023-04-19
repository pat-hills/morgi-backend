<?php

use App\FaceRecognition\AwsFaceRekognitionCollectionUtils;
use App\Enums\FaceRecognitionCollectionEnum;

/*
 * This test is commented cause we cant create a new collection every time, it costs a lot :)
 */
/*test("Test create new face recognition collection", function () {

    $utils = new AwsFaceRekognitionCollectionUtils();

    try {
        $collection = $utils->createCollection(FaceRecognitionCollection::TYPE_ROOKIE_FEMALE);
    }catch (Exception $exception){
        throw new Exception($exception->getMessage());
    }

    expect($collection)->not->toBeNull()
        ->is_full->toBe(false)
        ->is_active->toBe(true)
        ->type->toBe(FaceRecognitionCollection::TYPE_ROOKIE_FEMALE);
});*/

test("Test get active collection for every type", function () {

    $utils = new AwsFaceRekognitionCollectionUtils();

    foreach (FaceRecognitionCollectionEnum::TYPES as $type){

        try {
            $collection = $utils->getOrCreateFirstAvailableCollection($type);
        }catch (Exception $exception){
            throw new Exception($exception->getMessage());
        }

        expect($collection)->not->toBeNull()
            ->is_full->toBe(false)
            ->is_active->toBe(true)
            ->type->toBe($type);
    }
});

test("Test make collection inactive/active/full/empty", function () {

    $utils = new AwsFaceRekognitionCollectionUtils();
    $type = FaceRecognitionCollectionEnum::TYPE_LEADER_MALE;

    try {
        $collection = $utils->getOrCreateFirstAvailableCollection($type);
    }catch (Exception $exception){
        throw new Exception($exception->getMessage());
    }

    $collection->makeInactive();
    expect($collection)->not->toBeNull()
        ->is_active->toBe(false)
        ->type->toBe($type);

    $collection->makeActive();
    expect($collection)->not->toBeNull()
        ->is_active->toBe(true)
        ->type->toBe($type);

    $collection->makeFull();
    expect($collection)->not->toBeNull()
        ->is_full->toBe(true)
        ->type->toBe($type);

    $collection->makeNotFull();
    expect($collection)->not->toBeNull()
        ->is_full->toBe(false)
        ->type->toBe($type);
});
