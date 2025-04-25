document.addEventListener("DOMContentLoaded", function () {
    if (typeof Tutor !== "undefined" && Tutor.CourseBuilder) {

        // var values = tlcfData.values;//JSON.parse(tlcfData.values);

        // console.log('values', values);

        var locations = tlcfData.values; //tlcfData.locations;

        console.log('locations', locations);
        
        var options = [];

        for(var i=0; i<locations.length; i++){
            options.push({
                label: locations[i],
                value: locations[i]
            })
        }

        // console.log(tlcfData.label);
        // console.log(tlcfData.locations);
        // console.log(tlcfData.values);

        // console.log('options', options);
        
        // locations.foreach(element=>{
        //     options.add({
        //         label: element,
        //         value: element,
        //     })
        // })

        // console.log('temp', temp);
        
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length) {
                    const element = document.querySelector('.css-19sk4h4');
                    if (element) {
                        add_field_to_tutor_courses_page();
                        // observer.disconnect(); // Stop observing once element is found

                        
                    }
                }
            });
        });
        
        // Start observing the document with the configured parameters
        observer.observe(document.body, { childList: true, subtree: true });
        

        function add_field_to_tutor_courses_page(){

            var temp = document.querySelector('.all-locations');
            if(temp) return;
            
            var container = document.createElement('div');

                container.classList = 'all-locations';
                // Append checkbox group to the page
                options.forEach(function(option) {
                    var checkboxContainer = document.createElement('div');
                    var checkbox = document.createElement('input');
                    var label = document.createElement('label');
                    
                    checkbox.type = 'checkbox';
                    checkbox.id = option.value;
                    checkbox.name = 'course_locations';
                    checkbox.value = option.value;
                    // checkbox.classList = 'course_location';
                    
                    label.htmlFor = option.value;
                    label.appendChild(document.createTextNode(option.label));
                    
                    checkboxContainer.appendChild(checkbox);
                    checkboxContainer.appendChild(label);
                    

                    container.appendChild(checkboxContainer);

                    if (tlcf_ajax_object.course_locations 
                        && tlcf_ajax_object.course_locations
                            .includes(option.value)) {
                                checkbox.checked = true;
                    }


                });

                var label = document.createElement('label');

                
                // label.htmlFor = option.value;
                label.appendChild(document.createTextNode(tlcfData.label));
                
                var spaceInBetween = document.createElement('div');
                
                spaceInBetween.style.height = '6px';
                
                container.prepend(spaceInBetween);
                container.prepend(label);

                var element = document.querySelector('.css-19sk4h4');

                element.parentElement.parentElement.parentElement.insertAdjacentElement("afterend", container);


                jQuery.ajax({
                    url: tlcf_ajax_object.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_course_locations',
                        course_id: tlcf_ajax_object.course_id,
                    }, 
                    success: function(response) {
                        if (response.success && response.data.locations) {
                            const checkboxes = document.querySelectorAll('input[name="course_locations"]');
                            checkboxes.forEach(checkbox => {
                                if (response.data.locations.includes(checkbox.value)) {
                                    checkbox.checked = true;
                                }
                            });
                        }
                    }
                });
    
    
                const checkboxes = document.querySelectorAll('input[name="course_locations"]');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        save_course_locations();
                    });
                });


                function save_course_locations(){

                    const checkboxes = document.querySelectorAll('input[name="course_locations"]:checked');
                    const selectedValues = Array.from(checkboxes).map(checkbox => checkbox.value);

                    // Send AJAX request to WordPress backend
                    jQuery.ajax({
                        url: tlcf_ajax_object.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'save_course_locations',
                            locations: selectedValues,
                            course_id: tlcf_ajax_object.course_id,
                            nonce: tlcf_ajax_object.nonce
                        }
                    });
                }
    
        } 

        
        // Register a textarea field
        Tutor.CourseBuilder.Basic.registerField("after_description", {
            name: "course_location",
            type: "select",
            label: "Course Location",
            placeholder: "Select Location...",
            priority: 20,
            options: options
        });

        // Register a number field
        Tutor.CourseBuilder.Curriculum.Lesson.registerField("bottom_of_sidebar", {
            name: "lesson_duration",
            type: "number",
            label: "Lesson Duration (minutes)",
            priority: 5,
        });
    }
});
