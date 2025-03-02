import React, {useContext,useEffect,useState} from 'react';
import { LoadingContext, AuthContext } from '../App';
import { useNavigate } from "react-router-dom";
import Select from 'react-select';
import makeAnimated from 'react-select/animated';

function CreateUnitTask() {
     let navigate = useNavigate();
     const animatedComponents = makeAnimated();
    const {setLoading} = useContext(LoadingContext);
    const {isAuth} = useContext(AuthContext);
    const [allUnits, setAllUnits] = useState([]);
    const [allWorkers, setAllWorkers] = useState([]);
    const [selectedWorker, setSelectedWorkers] = useState([]);
    

    useEffect(()=>{
        getAllUnits();
        getAllWorkers();
    },[]);

    async function getAllUnits(){ 
        try{
            let response = await fetch('http://localhost/mini-axami/public/api/getAllCompanyUnits',{
                method:'GET',
                headers: { 'Content-Type': 'application/json' },
            });
            let serverRes = await response.json();

            if(!response.ok){
                console.log(serverRes.error || "Hämtning av units misslyckades. Försök igen.");
            }

            console.log(serverRes['success']);
            if (serverRes['success']){
              setAllUnits(serverRes.success);
            } else {
                console.log(serverRes.error || "Server Error occurred...");
            }
        }catch (error) {
            console.error("Fel vid hämtning av Units:", error);
           
        }
    }

    async function getAllWorkers(){
        try{
            const res = await fetch('http://localhost/mini-axami/public/api/getAllCompanyWorkers',{
                method:'GET',
                headers:{'Content-Type' : 'application/json'},
            }); 
    
            const jsonRes = await res.json();

            if(!res.ok) return console.log("Fetch failed");

            if(jsonRes['success']){
                console.log(jsonRes['success']);
                setAllWorkers(jsonRes['success']);
            }else{
                return console.error("ERROR: " + jsonRes['error'])
              }
        }catch(error){
            console.error("Fel vid hämtning av workers:", error);
        }
    }

    const handleCreation = async (event) =>{
        event.preventDefault();
        setLoading(true);
/* if(!isset($data['category'], $data['description'], $data['title'], $data['unit_id'], $data['assigned_workers'])){ */
        let body = JSON.stringify({
            title: event.target.title.value,
            description: event.target.description.value,
            category: event.target.category.value,
            unit_id: event.target.unitChoice.value,
            assigned_workers: selectedWorker.map(worker => worker.value)
        });

        console.log("body:::::");
        console.log(body);

        try{
            let response = await fetch('http://localhost/mini-axami/public/api/createTask', {
                method: 'POST',
                credentials: "include",
                headers: {
                    'Content-Type': 'application/json'
                },
                body: body 
            });
            const taskRes = await response.json();

            if(taskRes.error) return console.log(taskRes.error);

            navigate('/showUnitTasks');


        }catch(error){
            console.log('Catch error: ' + error);
        }finally{
            setLoading(false);
        }
    }

    const handleCancel = () =>{
        navigate('/home');
    }
/* if(!isset($data['category'], $data['description'], $data['title'], $data['unit_id'], $data['assigned_workers'])){ */
    return ( <>
    {isAuth ? 
        <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Skapa UnitTask</h3>
                <form onSubmit={handleCreation} onReset={handleCancel}>
                    <label className="form-label">Välj Unit för Task:</label>
                    {allUnits ? 
                            <Select
                                name="unitChoice"
                                options={allUnits.map(unit => ({ value: unit.id, label: unit.name }))}
                                classNamePrefix="Välj Unit"
                                placeholder="Välj en Unit"
                                required
                            />
                            : <></>
                        }
                    <div className="mb-3">
                        <label className="form-label">Titel</label>
                        <input className="form-control" type="text" name="title" placeholder="Titel för uppgiften" required />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Beskrivning</label>
                        <textarea className="form-control" type="text" name="description" placeholder="Beskrivning av uppgiften" required />
                    </div>

                    <label className="form-label">Välj kategori</label>
                    <Select
                        name="category"
                        options={[
                            { value: "Maintenance", label: "Maintenance" },
                            { value: "Repair", label: "Repair" },
                            { value: "Installation", label: "Installation" },
                            { value: "Monitoring", label: "Monitoring" },
                            { value: "Inspection", label: "Inspection" },
                            { value: "Cleaning", label: "Cleaning" },
                            { value: "Upgrading", label: "Upgrading" },
                            { value: "Troubleshooting", label: "Troubleshooting" }
                        ]}
                        classNamePrefix="Välj Kategori"
                        placeholder="Välj en kategori"
                        required
                    />


                    <label className="form-label">Tilldela uppgiften:</label>
                    {allWorkers ?
                        <Select
                            isMulti
                            name="assignedWorkers"
                            options={allWorkers.map(worker => ({ value: worker.id, label: worker.name }))} //Behöver vara key value pair
                            className="basic-multi-select"
                            classNamePrefix="Tilldela Worker"
                            placeholder="Tilldela en eller flera workers"
                            components={animatedComponents} 
                            value={selectedWorker}
                            onChange={setSelectedWorkers}
                            
                        />
                        : <></>
                    }

                    <div className="d-flex justify-content-between mt-3">
                        <button type="submit" className="btn btn-primary w-50 me-2">Skapa</button>
                        <button type="reset" className="btn btn-secondary w-50">Avbryt</button>
                    </div>
                </form>
            </div>
        </div>
        :
        <></>
        }
    </> );
}

export default CreateUnitTask;