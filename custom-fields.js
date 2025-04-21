document.addEventListener("DOMContentLoaded", function () {
    if (typeof Tutor !== "undefined" && Tutor.CourseBuilder) {

        // console.log(tlcfData.label);
        // console.log(tlcfData.locations);
        // console.log(tlcfData.values);

        var locations = tlcfData.locations;

        var options = [];
        locations.foreach(element=>{
            options.add({
                label: element,
                value: element,
            })
        })

        console.log('temp', temp);
        
        
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
