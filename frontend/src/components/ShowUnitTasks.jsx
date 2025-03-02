import { useState, useEffect, useContext } from "react";
import { Link,useNavigate } from "react-router-dom";
import { LoadingContext } from "../App";

function showUnitTasks() {
    const [allTasks, setAllTasks] = useState([]);
    const {setLoading} = useContext(LoadingContext);
    const navigate = useNavigate();

    useEffect(()=>{
        getAllTasks();
    }, []);

    async function getAllTasks(){
        try{
            const res = await fetch('http://localhost/mini-axami/public/api/getUnitTasks',{
                method:'GET',
                headers:{'Content-Type' : 'application/json'},
            }); 
    
            const jsonRes = await res.json();

            if(!res.ok) return console.log("Fetch failed");

            if(jsonRes['success']){
                console.log(jsonRes['success']);
                setAllTasks(jsonRes['success']);
            }else{
                return console.error("ERROR: " + jsonRes['error'])
              }
        }catch(error){
            console.error("Fel vid hämtning av workers:", error);
        }
    }

    return ( <>
        {JSON.stringify(allTasks)}
    </> );
}

export default showUnitTasks;