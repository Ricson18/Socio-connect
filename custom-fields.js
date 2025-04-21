document.addEventListener("DOMContentLoaded", function () {
    if (typeof Tutor !== "undefined" && Tutor.CourseBuilder) {
        // Register a textarea field
        Tutor.CourseBuilder.Basic.registerField("after_description", {
            name: "course_location",
            type: "select",
            label: "Course Location",
            placeholder: "Select Location...",
            priority: 20,
            options: [
                {
                    label: "Canada 2",
                    value: "Canada",
                },
                {
                    label: "United States",
                    value: "United States",
                },
                {
                    label: "Las Vegas",
                    value: "Las Vegas",
                },
                {
                    label: "Miami",
                    value: "Miami",
                },
            ],

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
