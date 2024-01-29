import { useEffect, useState } from "react";
import reactLogo from "./assets/react.svg";
import viteLogo from "./assets/vite.svg";
import "./styles/AppBack.scss";
import { useCreateCourse } from "./hooks/useCreateCouse";
import { RequestCourse } from "./hooks/Requests/RequestCourse";
import { useCreateLession } from "./hooks/useCreateLession";
import { RequestLession } from "./hooks/Requests/RequestLession";
import RequestQuiz from "./hooks/Requests/RequestQuiz";
import { cfact_project } from "./hooks/CourseFactory/CFact_Project";
import { useCreateTopic } from "./hooks/useCreateTopic";
import { RequestTopic } from "./hooks/Requests/RequestTopic";
import CFact_LD_Section_Heading from "./class/CFact_LD_Section_Heading";
import { useCreateQuiz } from "./hooks/useCreateQuiz";
import { useCreateQuestion } from "./hooks/useQuestion";
import RequestQuestion from "./hooks/Requests/RequestQuestions";
import { LD_AwserData_item, LD_AwserData } from "./hooks/LD_AwnserData";
import { useCreateAwnser } from "./hooks/useAwnser";

function crearCurso(data, callback) {
  const nuevoCurso = RequestCourse(
    data.slug,
    data.title,
    data.description,
    "",
    "publish",
    1
  );

  useCreateCourse(nuevoCurso, (response) => {
    callback(response);
  });
}

function crearLecciones() {
  const nuevaLeccion = RequestLession(
    "leccion-desde-la-api",
    "Leccion desde la API",
    "",
    "",
    "publish",
    1,
    7
  );

  useCreateLession(nuevaLeccion, (res) => {
    console.log(res);
  });
}

function crearQuiz() {
  const nuevoQuiz = RequestQuiz(
    "quiz-desde-la-api",
    "Quiz desde la API",
    "",
    "",
    "publish",
    1,
    7
  );

  useCreateQuiz(nuevoQuiz, (res) => {
    console.log(res);
  });
}

const CF_Proyect = ({ title, proyecto, index }) => {
  const [imported, setImported] = useState(false);
  const [proyect, setProject] = useState({});

  useEffect(() => {}, []);

  function importProyect() {
    const {
      generated_title,
      generated_description,
      outcome_list,
      structure_list,
      version,
      id,
    } = proyecto;

    crearCurso(
      {
        title: generated_title,
        slug: generated_title,
        description: generated_description,
      },
      (e) => {
        const course_id = e.id;

        generateSections(course_id);

        importarLessions(course_id);

        const curso_cf = {
          outcome_list,
          structure_list,
          version,
          course_id,
          id,
        };

        wp.apiRequest({
          path: "/cfact/v1/cfact-curso-cf",
          method: "POST",
          data: JSON.stringify(curso_cf),
          // ... otros parametros
        })
          .then((response) => {
            console.log(response);
          })
          .catch((error) => {
            console.log(error);
          });
      }
    );
  }

  function importarLessions(course_id) {
    const { structure_list, id } = proyecto;

    structure_list.forEach((sections) => {
      const { id, title, sub_structure_list } = sections;

      sub_structure_list.forEach((lession) => {
        //console.log(sub_lession);

        const topics_list = lession.sub_structure_list;

        const nuevaLeccion = RequestLession(
          lession.title,
          lession.title,
          lession.description,
          "",
          "publish",
          1,
          course_id
        );

        useCreateLession(nuevaLeccion, (e) => {
			
          const type = e.type.name;

          importarTopics(course_id, e.id, topics_list, type);

        });
      });
    });

    setImported(true);
    alert("Importado con exito");
  }

  function importarTopics(course_id, lesson_id, topics_list) {

    topics_list.forEach((topic) => {
      const content_version = topic.content_version_id;
      console.log(content_version);

      const nuevoTopic = RequestTopic(
        topic.title,
        topic.title,
        topic.description,
        "",
        "publish",
        1,
        course_id,
        lesson_id
      );

	  switch (topic.type.name) {
		case "quiz":

			if (content_version !== null) {

				importarContenido(content_version, "", (response) => {
					
					const { title, description, content_list } = response.data;
	
					const nuevoQuiz = RequestQuiz(
						title,
						title,
						description,
						"",
						"publish",
						1,
						course_id,
						lesson_id
					);
	
					// Step 1. Create Quiz.
	
					useCreateQuiz(nuevoQuiz, (e) => {

						const quiz_id = e.id;

            // Step 2. Create Questions.

            content_list.forEach(question => {

                const {content, content_list} = question;

                const respuestas = [];

                content_list.forEach(e => {
                  const {content, is_answer} = e;

                  respuestas.push({"post_title": content, "correct": is_answer});

                });

                const nuevaPregunta = {
                    "post_title" : content,
                    "post_content" : content,
                    "quiz_id": quiz_id,
                    "nonce": wpApiSettings.nonce,
                    "cookie": wpApiCookie,
                    "answer": respuestas
                };

                console.log("Antes de crear pregunta");
                useCreateAwnser(nuevaPregunta, (e) => {
                  console.log(e);
                });
                console.log("Despues de crear pregunta");
                

                //console.log(nuevaPregunta);

            });
            
					});
	
					console.log(content_list);
	
			   });
	
				  
			}
			break;
		case "reading":
			useCreateTopic(nuevoTopic, (e) => {
				const id = e.id;
		
				if (content_version !== null) {
				  console.log("esta entrando a importarContenido");
				  importarContenido(content_version, id, (response) => {
					console.log(response);
				  });
				}
		
				wp.apiRequest({
				  path: "/cfact/v1/cfact-tema-cf",
				  method: "POST",
				  data: JSON.stringify({
					id: id,
					topic_type: topic.type.name,
				  }),
				})
				  .then((response) => {
					console.log(response);
				  })
				  .catch((error) => {
					console.log(error);
				  });
			  });
			break;
		case "video":
			useCreateTopic(nuevoTopic, (e) => {
				const id = e.id;
		
				if (content_version !== null) {
				  console.log("esta entrando a importarContenido");
				  importarContenido(content_version, id, (response) => {
					console.log(response);
				  });
				}
		
				wp.apiRequest({
				  path: "/cfact/v1/cfact-tema-cf",
				  method: "POST",
				  data: JSON.stringify({
					id: id,
					topic_type: topic.type.name,
				  }),
				})
				  .then((response) => {
					console.log(response);
				  })
				  .catch((error) => {
					console.log(error);
				  });
			  });
			break;
		default:
			break;
	  }

      
    });
  }

  function generateSections(course_id) {
    const { structure_list, id } = proyecto;

    // Inicializo un array de secciones.
    const sections = [];

    let offset = 0;

    structure_list.forEach((section, index) => {
      const { id, title, sub_structure_list } = section;

      const counter = index + 1 + offset;

      const nuevaSeccion = new CFact_LD_Section_Heading(counter, title);

      // Guardar la seccion en el array.
      sections.push(nuevaSeccion);

      const leccions_length = sub_structure_list.length;
      offset += leccions_length;
    });

    const raw = JSON.stringify({
      "course-section": sections,
      course_id: course_id,
    });

    wp.apiRequest({
      path: "/cfact/v1/cfact-course-section",
      method: "POST",
      data: raw,
    })
      .then((response) => {
        console.log(response);
      })
      .catch((error) => {
        console.log(error);
      });
  }

  function importarContenido(content_version, topic_id, callback) {
    wp.apiRequest({
      path: `/cfact/v1/cfact-content?content_version_id=${content_version}&topic_id=${topic_id}}`,
      method: "GET",
    })
      .then((response) => {

        callback(response);

      })
      .catch((error) => {
        console.log(error);
      });
  }

  return (
    <div className="cfact-proyecto-item col-4">
      <div className="cfact-proyecto-item__inside">
        <h3>{title}</h3>
        {imported && <span>Importado</span>}
        <div className="cfact-proyecto-item__footer">
          <p>Version: {proyecto.version}</p>
          {
            <button className="btn btn-cfact" onClick={(e) => importProyect(e)}>
              Importar Curso
            </button>
          }
          <button
            onClick={(e) => generateSections(954)}
            className="btn btn-cfact"
          >
            Generate Section
          </button>
        </div>
      </div>
    </div>
  );
};

function App() {
  const [projectList, setProjectList] = useState([]);

  useEffect(() => {
    wp.apiRequest({
      path: "/cfact/v1/cfact-projects",
      method: "GET",
      // ... otros parametros
    })
      .then((response) => {
        setProjectList([response]);
      })
      .catch((error) => {
        console.log(error);
      });
  }, []);

  return (
    <div className="AppBack">
      <button
        onClick={(e) =>
          useCreateQuestion(RequestQuestion(), (e) => console.log(e))
        }
      >
        Crear Quiz
      </button>
      <div className="header mb-3">
        <h1>Course Factory Integration</h1>
      </div>

      <div className="cfact-proyecto">
        {projectList.length > 0 &&
          projectList[0].last_version.map((item, index) => {
            return (
              <CF_Proyect
                index={index}
                key={index}
                proyecto={item}
                title={item.generated_title}
              />
            );
          })}

        {projectList.length == 0 && (
          <>
            <h5>Cargando ... </h5>
            <div
              className="spinner-grow cfact-color cfact-color-dark"
              role="status"
            >
              <span className="sr-only">Loading...</span>
            </div>
            <div className="spinner-grow cfact-color" role="status">
              <span className="sr-only">Loading...</span>
            </div>
            <div
              className="spinner-grow cfact-color cfact-color-dark"
              role="status"
            >
              <span className="sr-only">Loading...</span>
            </div>
          </>
        )}
      </div>
    </div>
  );
}

export default App;
