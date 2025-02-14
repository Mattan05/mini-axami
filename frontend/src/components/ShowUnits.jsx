import { useState,  useEffect, useContext, useMemo } from "react";
import { LoadingContext } from "../App";
import Unit from "./Unit";
function ShowUnits() {
    const {setLoading} = useContext(LoadingContext);
    const [units, setUnits] = useState([]);
    const [unitChanged, setUnitChanged] = useState([]);

    useEffect(() => {
        loadUnits(); 
      }, []);

      async function loadUnits() {
       /*  setLoading(true); */
        try {
          const res = await fetch('http://localhost/mini-axami/public/api/showUnits');
          const data = await res.json();

          if (!res.ok) {
            throw new Error("Något gick fel med att hämta enheter.");
        }

          if (data['success']) {
            setUnits(data.success);
          }
        } catch (error) {
          console.error("Fel vid hämtning av units:", error);
        }/* finally {
          setLoading(false);
        } */
      }
      
    return ( <>
    <h1><strong>Alla Units:</strong></h1>

    {units.length > 0 ? (
          units.map((unit) => (
              <Unit key={"fav_"+unit.id} unit={unit}/>
          ))
        ) : (
          /* KNAPP FÖR ATT SKAPA EN NY */
            <p className="text-light text-center">Inga Units ännu...</p>
        )}

     

    </> );
}

export default ShowUnits



