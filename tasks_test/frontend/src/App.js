import React, { useState, useEffect } from "react";
import './App.css';
import axios from "axios";

const API_URL = "http://localhost:8080/api/tasks"; // Declaración fuera del JSX

function App() {
  const [tasks, setTasks] = useState([]);
  const [form, setForm] = useState({ title: "", description: "" });

  // Obtener tareas del backend
  const getTasks = async () => {
    try {
      const res = await axios.get(API_URL);
      setTasks(res.data);
    } catch (err) {
      console.error(err);
    }
  };

  // Crear nueva tarea
  const createTask = async (e) => {
    e.preventDefault();
    if (!form.title.trim()) return alert("El título es obligatorio");
    await axios.post(API_URL, { ...form, status: "pendiente" });
    setForm({ title: "", description: "" });
    getTasks();
  };

  // Cambiar estado de tarea
  const updateStatus = async (id, status) => {
    const nextStatus = status === "pendiente" ? "en_progreso" : status === "en_progreso" ? "completada" : "pendiente";
    await axios.put(`${API_URL}/${id}`, { status: nextStatus });
    getTasks();
  };

  // Eliminar tarea
  const deleteTask = async (id) => {
    await axios.delete(`${API_URL}/${id}`);
    getTasks();
  };

  useEffect(() => {
    getTasks();
  }, []);

  return (
    <div className="App">
      <header className="App-header">
        <h2>Gestión de Tareas</h2>

        <form onSubmit={createTask}>
          <input
            type="text"
            placeholder="Título"
            value={form.title}
            onChange={(e) => setForm({ ...form, title: e.target.value })}
            required
          />
          <textarea
            placeholder="Descripción"
            value={form.description}
            onChange={(e) => setForm({ ...form, description: e.target.value })}
          />
          <button type="submit">Crear</button>
        </form>

        <ul style={{ listStyle: "none", padding: 0 }}>
          {tasks.map((t) => (
            <li key={t.id} style={{ margin: "10px 0", borderBottom: "1px solid #ccc" }}>
              <strong>{t.title}</strong> <em>({t.status})</em>
              <br />
              {t.description && <small>{t.description}</small>}
              <div>
                <div>
                  <button onClick={() => updateStatus(t.id, t.status)}>
                    {t.status === "pendiente" ? "Empezar" : t.status === "en_progreso" ? "Completar" : "Reiniciar"}
                  </button>
                  <button onClick={() => deleteTask(t.id)}>Eliminar</button>
                </div>
              </div>
            </li>
          ))}
        </ul>
      </header>
    </div>
  );
}

export default App;
