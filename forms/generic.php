<?php
//
// Description
// -----------
// This function returns the generic animal adoption form.
//
// Arguments
// ---------
// ciniki:
// tnid:
//
// Returns
// -------
//
function ciniki_petadoptions_forms_generic($ciniki, $tnid) {
    
    $form = array(
        'sections' => array(
            '1' => array(
                'name' => 'Animal Information',
                'fields' => array(
                    'animal_name' => array('label' => "Animal's Name", 'required' => 'yes', 'type' => 'text', 'description' => "Who is the dog you wish to adopt?"),
                    'second_choice' => array('label' => "Options", 'required' => 'no', 'type' => 'text', 'description' => "If this dog is no longer available, is there another dog you would be interested in?"),
                    'agree' => array('label' => "Important Information", 'required' => 'yes', 'type' => 'checkboxes', 'description' => "I understand that this application form is not a guarantee that the dog I have applied for will still be available if my application to adopt a dog is approved, as there could be several applications for this dog already in progress or the dog may have a requirement that my home does not provide.\n\n"
                        . "I understand that Stray Paws from Greece does not approve their dogs to applicants on a \"first come first serve\" basis, it is the best interest of the dog that is considered in the event of multiple applications for the same dog and the home best suited to the specific needs of the dog will be selected.\n\n"
                        . "Stray Paws from Greece reserves the right to refuse any adoption application if it feels the adoption is not in the best interests of the dog.\n\n"
                        . "I understand that no matter how much rescued dogs are evaluated by their temporary care families, they usually come with \"baggage\" we don't know about, and the transition may take weeks or even months.\n\n"
                        . "Falsified answers to any question is this application or asked as part of the application process will lead to automatic refusal of this application.\n\n",
                        'options' => array('I understand this information and wish to continue'),
                    ),
                ),
            ),
            '2' => array(
                'name' => 'About you',
                'fields' => array(
                    'first_name' => array('label' => "First Name", 'required' => 'yes', 'type' => 'text', 'size' => 'medium'),
                    'last_name' => array('label' => "Last Name", 'required' => 'yes', 'type' => 'text', 'size' => 'medium'),
                    'age' => array('label' => "Age of Adopter", 'required' => 'yes', 'type' => 'text'),
                    'address1' => array('label' => "Address", 'required' => 'yes', 'type' => 'text'),
                    'address2' => array('label' => "", 'required' => 'no', 'type' => 'text'),
                    'city' => array('label' => "City", 'required' => 'yes', 'type' => 'text', 'size' => 'medium'),
                    'province' => array('label' => "Province", 'required' => 'yes', 'type' => 'text', 'size' => 'medium'),
                    'postal_code' => array('label' => "Postal Code", 'required' => 'yes', 'type' => 'text', 'size' => 'medium'),
                    'contact_method' => array('label' => "Preferred method of contact", 'required' => 'yes', 'type' => 'radio', 'options' => array('Home phone', 'Cell phone', 'Work phone')),
                    'home_phone' => array('label' => "Home Phone", 'required' => 'no', 'type' => 'text', 'size' => 'small'),
                    'cell_phone' => array('label' => "Cell Phone", 'required' => 'no', 'type' => 'text', 'size' => 'small'),
                    'work_phone' => array('label' => "Work Phone", 'required' => 'no', 'type' => 'text', 'size' => 'small'),
                    'email' => array('label' => "Email", 'required' => 'yes', 'type' => 'text', 'size' => 'medium'),
                    'email_confirm' => array('label' => "Confirm Email", 'required' => 'yes', 'type' => 'text', 'size' => 'medium'),
                    'number_adults' => array('label' => "Number of Adults in your Household", 'required' => 'yes', 'type' => 'select', 'options' => array('1', '2', '3', '4', '5', 'more than 5')),

                    'children' => array('label' => "Are there children in the household?", 'required' => 'yes', 'type' => 'radio', 'options' => array('No', 'Yes')),
                    'number_children' => array('label' => "Number of Children in your Household", 'required' => 'yes', 'type' => 'select', 'options' => array('1', '2', '3', '4', '5', 'more than 5'), 'size' => 'medium', 'conditions' => array(array('section'=>'2', 'field'=>'children', 'value'=>'Yes'))),
                    'childrens_ages' => array('label' => "Children's Ages", 'required' => 'yes', 'type' => 'text', 'conditions' => array(array('section'=>'2', 'field'=>'children', 'value'=>'Yes')), 'size' => 'medium', 'conditions' => array(array('section'=>'2', 'field'=>'children', 'value'=>'Yes'))),

                    'allergies' => array('label' => "Allergies", 'required' => 'yes', 'type' => 'radio', 'options' => array('No', 'Yes')),
                    'allergies_details' => array('label' => "Allergies, please explain", 'required' => 'yes', 'type' => 'textarea', 'description' => "If there are allergies in the household, please explain", 'conditions' => array(array('section'=>'2', 'field'=>'allergies', 'value'=>'Yes'))),
                    'family_agreement' => array('label' => "Family Agreement", 'required' => 'yes', 'type' => 'radio', 'description' => "Have you talked to all family members and occupants in your home about adding a dog to the household and do they agree?", 'options' => array('No', 'Yes')),
                ),
            ),
            '3' => array(
                'name' => 'Your Lifestyle/Household Information',
                'fields' => array(
                    'property_type' => array('label' => "This is a", 'required' => 'yes', 'type' => 'radio', 'options' => array('House', 'Apartment/Condo')),
                    'property_ownership' => array('label' => "Own/Rent", 'required' => 'yes', 'type' => 'radio', 'description' => "Do you own or rent this property?", 'options' => array('Own', 'Rent')),
                    'property_permission' => array('label' => "I confirm I have permission from the owner(s) of this property to have this animal", 'required' => 'yes', 'type' => 'radio', 'options' => array('No', 'Yes'), 'conditions' => array(array('section'=>'3', 'field'=>'property_ownership', 'value'=>'Rent'))),
                    'landlord_name' => array('label' => "Landlord's Name", 'required' => 'yes', 'type' => 'text', 'description' => "Please provide the name of the apartment complex and the landlord's name and phone number so we can verify that animals are allowed.", 'conditions' => array(array('section'=>'3', 'field'=>'property_ownership', 'value'=>'Rent'))),
                    'landlord_phone' => array('label' => "Landlord's Phone Number", 'required' => 'yes', 'type' => 'text', 'conditions' => array(array('section'=>'3', 'field'=>'property_ownership', 'value'=>'Rent'))),
                    'backyard' => array('label' => "Backyard", 'required' => 'yes', 'type' => 'radio', 'description' => "Do you have a fenced backyard?", 'options' => array('No', 'Yes'), 'text_option' => 'Other'),
                    'backyard_size' => array('label' => "Backyard Size", 'required' => 'yes', 'type' => 'text', 'description' => "Approximately how large is your backyard?", 'conditions' => array(array('section'=>'3', 'field'=>'backyard', 'value'=>'Yes'))),
                    'backyard_gate' => array('label' => "Is there a gate?", 'required' => 'yes', 'type' => 'text', 'description' => "", 'conditions' => array(array('section'=>'3', 'field'=>'backyard', 'value'=>'Yes'))),
                    'activity_level' => array('label' => "Activity Level", 'required' => 'yes', 'type' => 'select', 'description' => "How physically active is your family?", 'options' => array('Very Active', 'Active', 'Somewhat Active', 'Not Active')),
                    'responsible_name' => array('label' => "Responsible Adult", 'required' => 'yes', 'type' => 'text', 'description' => "Which adult member of your household will be primarily responsible for feeding, training and caring for your new dog??"),
                    'move' => array('label' => "What will you do with your dog IF you ever move?", 'required' => 'no', 'type' => 'textarea'),
                    'vacation' => array('label' => "What will you do with your dog IF you go on vacation?", 'required' => 'no', 'type' => 'textarea'),
                    'day' => array('label' => "Where will your dog be kept during the day?", 'required' => 'no', 'type' => 'textarea'),
                    'night' => array('label' => "Where will your dog be sleep at night?", 'required' => 'no', 'type' => 'textarea'),
                    'alone' => array('label' => "How many hours will your dog be alone?", 'required' => 'no', 'type' => 'textarea'),
                    'expenses' => array('label' => "Pet Expenses", 'required' => 'no', 'type' => 'text', 'description' => "How much do you ESTIMATE it will cost you to care for your dog each year?"),
                    'training' => array('label' => "Training", 'required' => 'no', 'type' => 'text', 'description' => "How much time are you planning to spend training, playing with or exercising your dog each day?"),
                    'financial' => array('label' => "Financial Commitment", 'required' => 'yes', 'type' => 'radio', 'description' => "Are you financially prepared to take good care of your dog, and are you able and willing to handle the costs of routine and emergency veterinary care?", 'options' => array('No', 'Yes')),
                    'dog_issues' => array('label' => "Dog Issues", 'required' => 'yes', 'type' => 'checkboxes', 'description' => "Which of the following DOG issues are you NOT willing to accept in your home?", 'options' => array('Barking', 'Separation Anxiety', 'House Training', 'Digging', 'Property Damage', 'Chewing', 'Jumping up on people', 'Shedding', 'Mouthing', 'I am willing to accept and work on all these issues')),
                    'training_methods' => array('label' => "Training Methods", 'required' => 'no', 'type' => 'checkboxes', 'description' => "Which of the following training methods/devices are you going to use to train your dog?", 'options' => array('Verbal corrections', 'Leash corrections', 'Electric underground fencing', 'Treats and praise', 'Electronic collar/shock collar', 'Choke chain collar', 'Prong collar', 'Bark collar', 'Gentle leader/No-pull harness type device', 'Clicker training', 'Dominance theory/Pack leader', 'Obedience classes in a group', 'Obedience classes one-on-one', 'Puppy socialization classes')),
                    'other_info' => array('label' => "Other", 'required' => 'no', 'type' => 'textarea', 'description' => 'Please list any of the above methods/devices that you are interested in learning more about:'),
                    'crating' => array('label' => "Do you plan on crating your dog?", 'required' => 'no', 'type' => 'radio', 'options' => array('No', 'Yes')),
                    'crating_details' => array('label' => "If yes, under what circumstances?", 'required' => 'no', 'type' => 'textarea', 'conditions' => array(array('section'=>'3', 'field'=>'crating', 'value'=>'Yes'))),
                ),
            ),
            '4' => array(
                'name' => 'Pet Owning History',
                'fields' => array(
                    'previous_pets' => array('label' => "Have you ever owned any pets before?", 'required' => 'yes', 'type' => 'radio', 'options' => array('Yes', 'No, this will be my first pet')),
                    'pet1' => array('label' => 'Pet #1', 'type' => 'label', 'description' => "Please tell us about the pets you currently own OR have owned in the last five (5) years:", 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'))),
                    'pet1_type' => array('label' => "Type of Pet", 'required' => 'required', 'type' => 'radio', 'options' => array('Dog', 'Cat'), 'text_option' => 'Other', 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet1_breed' => array('label' => "Breed of Animal", 'required' => 'no', 'type' => 'text', 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet1_age' => array('label' => "Age of Animal Now", 'required' => 'no', 'type' => 'text', 'description' => "If animal is deceased please leave blank.", 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet1_sex' => array('label' => "Sex", 'required' => 'no', 'type' => 'radio', 'options' => array('Male', 'Female'), 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet1_fixed' => array('label' => "Is (was) the animal fixed (spayed or neutered)?", 'required' => 'no', 'type' => 'radio', 'options' => array('No', 'Yes'), 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet1_vac' => array('label' => 'Date of last Vaccination', 'required' => 'no', 'type' => 'date', 'description' => "Please provide the date of last vaccination (approximately)", 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet1_own' => array('label' => "Do you still own this animal?", 'required' => 'yes', 'type' => 'radio', 'options' => array('No', 'Yes'), 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet1_own_reason' => array('label' => "Why do you no longer own this animal?", 'required' => 'yes', 'type' => 'text', 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet1_own', 'value'=>'No'))),
                    'pet1_more' => array('label' => "Any more pets?", 'required' => 'yes', 'type' => 'radio', 'options' => array('No', 'Yes'), 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'))),

                    'pet2' => array('label' => 'Pet #2', 'type' => 'label', 'description' => "Please tell us about the second pet you owned.", 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet1_more', 'value'=>'Yes'))),
                    'pet2_type' => array('label' => "Type of Pet", 'required' => 'required', 'type' => 'radio', 'options' => array('Dog', 'Cat'), 'text_option' => 'Other', 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet1_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet2_breed' => array('label' => "Breed of Animal", 'required' => 'no', 'type' => 'text', 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet1_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet2_age' => array('label' => "Age of Animal Now", 'required' => 'no', 'type' => 'text', 'description' => "If animal is deceased please leave blank.", 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet1_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet2_sex' => array('label' => "", 'required' => 'no', 'type' => 'radio', 'options' => array('Male', 'Female'), 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet1_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet2_fixed' => array('label' => "Is (was) the animal fixed (spayed or neutered)?", 'required' => 'no', 'type' => 'radio', 'options' => array('No', 'Yes'), 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet1_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet2_vac' => array('label' => 'Date of last Vaccination', 'required' => 'no', 'type' => 'date', 'size' => 'medium', 'description' => "Please provide the date of last vaccination (approximately)", 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet1_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet2_own' => array('label' => "Do you still own this animal?", 'required' => 'yes', 'type' => 'radio', 'options' => array('No', 'Yes'), 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet1_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet2_own_reason' => array('label' => "Why do you no longer own this animal?", 'required' => 'yes', 'type' => 'text', 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet2_own', 'value'=>'No'), array('section'=>'4', 'field'=>'pet1_more', 'value'=>'Yes'))),
                    'pet2_more' => array('label' => "Any more pets?", 'required' => 'yes', 'type' => 'radio', 'description' => "Please tell us about any other pets currently owned or owned in the last 5 years.", 'options' => array('No', 'Yes'), 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet1_more', 'value'=>'Yes'))),

                    'pet3' => array('label' => 'Pet #3', 'type' => 'label', 'description' => "Please tell us about the third pet you owned.", 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet2_more', 'value'=>'Yes'))),
                    'pet3_type' => array('label' => "Type of Pet", 'required' => 'required', 'type' => 'radio', 'options' => array('Dog', 'Cat'), 'text_option' => 'Other', 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet2_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet3_breed' => array('label' => "Breed of Animal", 'required' => 'no', 'type' => 'text', 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet2_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet3_age' => array('label' => "Age of Animal Now", 'required' => 'no', 'type' => 'text', 'description' => "If animal is deceased please leave blank.", 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet2_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet3_sex' => array('label' => "", 'required' => 'no', 'type' => 'radio', 'options' => array('Male', 'Female'), 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet2_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet3_fixed' => array('label' => "Is (was) the animal fixed (spayed or neutered)?", 'required' => 'no', 'type' => 'radio', 'options' => array('No', 'Yes'), 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet2_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet3_vac' => array('label' => 'Date of last Vaccination', 'required' => 'no', 'type' => 'date', 'description' => "Please provide the date of last vaccination (approximately)", 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet2_more', 'value'=>'Yes')), 'size' => 'medium'),
                    'pet3_own' => array('label' => "Do you still own this animal?", 'required' => 'yes', 'type' => 'radio', 'options' => array('No', 'Yes'), 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet2_more', 'value'=>'Yes'))),
                    'pet3_own_reason' => array('label' => "Why do you no longer own this animal?", 'required' => 'yes', 'type' => 'text', 'conditions' => array(array('section'=>'4', 'field'=>'previous_pets', 'value'=>'Yes'), array('section'=>'4', 'field'=>'pet2_own', 'value'=>'No'), array('section'=>'4', 'field'=>'pet2_more', 'value'=>'Yes'))),
                ),
            ),
            '5' => array(
                'name' => 'Other Information',
                'fields' => array(
                    'vet' => array('label' => 'Veterinary Information', 'type' => 'label', 'description' => ""),
                    'vet_client' => array('label' => "Which veterinary clinic will you be using for this pet's veterinary care?", 'type' => 'text', 'required' => 'yes', 'description' => ""),
                    'vet_number' => array('label' => "Clinic's phone number", 'type' => 'text', 'required' => 'yes', 'description' => ""),
                    'vet_past' => array('label' => "Is this the clinic you have used for your pets in the past?", 'type' => 'radio', 'required' => 'yes', 'description' => "", 'options' => array('No', 'Yes')),
                    'vet_pastname' => array('label' => "Name of veterinary clinic you have used in the past", 'type' => 'text', 'required' => 'no', 'description' => "", 'conditions' => array(array('section'=>'5', 'field'=>'vet_past', 'value'=>'No'))),
                    'vet_pastnumber' => array('label' => "Past Clinic's phone number", 'type' => 'text', 'required' => 'no', 'description' => "", 'conditions' => array(array('section'=>'5', 'field'=>'vet_past', 'value'=>'No'))),
                    'vet_permission' => array('label' => "Permission to contact veterinarian clinic", 'type' => 'radio', 'required' => 'no', 'description' => "Do you give us permission to contact your vet in order to discuss your past and present pets' medical records?", 'options' => array('No', 'Yes', 'Not applicable, this will be my first pet')),
                    'references' => array('label' => "References", 'type' => 'label', 'description' => "Please provide two reference names of individuals who have: Known you for at least 3 years AND who know you as a pet owner OR can vouch for your overall responsibility."),
                    'ref1' => array('label' => "Reference #1", 'type' => 'label', 'description' => ""),
                    'ref1_name' => array('label' => "Name", 'required' => 'yes', 'type' => 'text', 'description' => ""),
                    'ref1_contact' => array('label' => "Contact Phone Number", 'required' => 'yes', 'type' => 'text', 'description' => ""),
                    'ref1_email' => array('label' => "Contact Email", 'required' => 'yes', 'type' => 'text', 'description' => ""),
                    'ref2' => array('label' => "Reference #2", 'type' => 'label', 'description' => ""),
                    'ref2_name' => array('label' => "Name", 'required' => 'yes', 'type' => 'text', 'description' => ""),
                    'ref2_contact' => array('label' => "Contact Phone Number", 'required' => 'yes', 'type' => 'text', 'description' => ""),
                    'ref2_email' => array('label' => "Contact Email", 'required' => 'yes', 'type' => 'text', 'description' => ""),
                    'ref3' => array('label' => "Reference #3", 'type' => 'label', 'description' => ""),
                    'ref3_name' => array('label' => "Name", 'required' => 'yes', 'type' => 'text', 'description' => ""),
                    'ref3_contact' => array('label' => "Contact Phone Number", 'required' => 'yes', 'type' => 'text', 'description' => ""),
                    'ref3_email' => array('label' => "Contact Email", 'required' => 'yes', 'type' => 'text', 'description' => ""),
                    'ref_permission' => array('label' => "Do you give us permission to contact your references to vouch for you as a responsible pet owner?", 'required' => 'yes', 'type' => 'radio', 'options' => array('No', 'Yes')),
                    'ref_homevisit' => array('label' => "Will you allow us to do a home visit in order to approve this adoption?", 'required' => 'yes', 'type' => 'radio', 'options' => array('No', 'Yes')),
                    'ref_period' => array('label' => "Adjustment Period", 'required' => 'yes', 'type' => 'radio', 'description' => "Are you willing to give a dog at least 2 months to adjust to you, your family and their new environment before you would even consider not keeping them?", 'options' => array('No', 'Yes')),
                    'questions' => array('label' => "Please list any questions you may have for the adoption counsellor?", 'required' => 'no', 'type' => 'textarea'),
                ),
            ),
            '6' => array(
                'name' => 'Terms',
                'fields' => array(
                    'terms' => array('label' => "Terms and Conditions", 'type' => 'label', 'description' => "By typing your name below, the applicant certifies that the information given is true, and you understand that Stray Paws from Greece reserves the right to deny your application for any reason at any time. You also further authorize the investigation of all statements and information in this application."),
                    'accept' => array('label' => "I have read and understand the above terms", 'required' => 'yes', 'type' => 'text'),
                    'todays_date' => array('label' => "Today's Date", 'required' => 'yes', 'type' => 'text', 'size' => 'small'),
                ),
            ),
        ),
        'thankyou_msg' => "Thank you for submitting your application, we will review your application and get back to you.",
    );

    return array('stat'=>'ok', 'form'=>$form);
}
?>
