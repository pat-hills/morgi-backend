<?php


namespace App\Utils;


class ReasonUtils
{
    /** NOTE: When you will add some values to other constant, please, insert them into @ALL_REASON */
    const ALL_REASON = [
        'forbidden_images_and_text' => 'Forbidden images and text. Please contact Customer Service for further details',
        'forbidden_content' => 'Forbidden content please adjust, if you require more information please contact support',
        'rookie_request' => 'Rookie request',
        'dissatisfaction_with_rookie' => 'Dissatisfaction with Rookie',
        'made_in_error' => 'Made in error',
        'technical' => 'Technical',
        'reject_payment' => 'Oh no, your payment was rejected. Please contact Morgi Customer Service for more information'
    ];

    const DECLINE_IDENTITY_DOCUMENT = [
        'details_do_not_match_to_profile' => 'Details do not match to profile',
        'low_quality' => 'Low quality',
        'id_type_not_excepted' => 'ID type not excepted',
        'id_not_valid' => 'ID not valid',
        'id_does_not_match_in_photos' => 'ID does not match in photos',
        'person_pictured_does_not_match_in_photos' => 'Person pictured does not match in photos'
    ];

    const NOTIFICATION_REASON_DECLINE_IDENTITY_DOCUMENT = [
        "details_do_not_match_to_profile" => "The ID does not match one or more details submitted on your Rookie profile. Please provide an explanation for this to customer support.",
        "low_quality" => "Due to the poor quality of your photo ID, we have been unable to confirm the information necessary to establish legal age and identification. Please take a new picture and resubmit it.",
        "id_type_not_excepted" => "The identification you have submitted is not acceptable as an official ID by Morgi. Please submit one of the following types of identification: A Valid Driver's License, Official Government ID or Passport. PLEASE NOTE: Your ID must include the following information: your full legal name, your official date of birth, your photo. Resubmit photo.",
        "id_not_valid" => "The ID you have provided is no longer valid. Please upload a valid ID. Resubmit photo.",
        "id_does_not_match_in_photos" => "We could not determine that the id was the same as the one you uploaded in the Administrative Snapshot due to the poor quality of the image. Please take a new picture and resubmit it.",
        "person_pictured_does_not_match_in_photos" => "The person pictured in the Administrative Snapshot is not the same as the one pictured on the ID uploaded. Please take a new picture and resubmit it."
    ];

    const FULL_DECLINE = array(
        'rookie' => array(
            'forbidden_images_and_text' => 'Forbidden images and text. Please contact Customer Service for further details'
        ),
        'leader' => array(
            'forbidden_images_and_text' => 'Forbidden images and text. Please contact Customer Service for further details',
        )
    );

    const DESCRIPTION_DECLINE = array(
        'rookie' => array(
            'forbidden_content' => 'Forbidden content please adjust, if you require more information please contact support'
        ),
        'leader' => array(
            'forbidden_content' => 'Forbidden content please adjust, if you require more information please contact support'
        )
    );

    const PHOTO_DECLINE = array(
        'rookie' => array(
            'forbidden_content' => 'Forbidden content please adjust, if you require more information please contact support'
        ),
        'leader' => array(
            'forbidden_content' => 'Forbidden content please adjust, if you require more information please contact support'
        )
    );

    const VIDEO_DECLINE = [
        'rookie' => [
            'forbidden_content' => 'Forbidden content please adjust, if you require more information please contact support'
        ],
    ];

    const ROOKIE_USERNAME_DECLINE = [
        'forbidden_content' => 'Forbidden content please adjust, if you require more information please contact support'
    ];

    const MICROMORGI_TAB_REFUND_REASON = array(

        'rookie' => array(
            'rookie_request' => 'Rookie request'
        ),

        'leader' => array(
            'dissatisfaction_with_rookie' => 'Dissatisfaction with Rookie',
            'made_in_error' => 'Made in error',
            'technical' => 'Technical'
        )

    );

    const ROOKIE_PAYMENT_REJECT = array(
        'reject_payment' => 'Oh no, your payment was rejected. Please contact Morgi Customer Service for more information'
    );
}
