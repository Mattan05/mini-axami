import React, {useContext} from 'react';
import { useEffect } from 'react';

import { LoadingContext, AuthContext } from '../App';
import { Link, useNavigate } from 'react-router-dom';

function Home() {
    const {isAuth, userId, setIsAuth, setUserId, userRole, setUserRole, userName, setUserName} = useContext(AuthContext);
    const navigate = useNavigate();

   /*  useEffect(() => {
        if (!isAuth) {
            navigate('/');
        }
    }, []); */ //HM checksession. DET ÄR FEL MED SESSION. I LOGIN SÅ REDIRECTAR JAG INNAN OMRENDERING EFTER SET FUNKTION PÅ USERID... HUR SKA JAG KONTROLLERA OM DET FINNS SESSION AKTIV PÅ PROTECTED ROUTES..
    

 /*    const handleProfilePage = () => {
        navigate('/customerProfile/'+userId);
    }; */
    return ( <>
    {isAuth ? 
    <>
        <h1 className='text-center text-info'>Välkommen <strong>{userName}</strong></h1>
      {/*   <strong className='text-muted'>Mini Axami Underhållssystem</strong> */}
{/* 
        <button className="btn btn-info" onClick={handleProfilePage}>ProfilSida</button> */}
        <Link className="btn btn-info" to="/customerProfile">ProfilSida</Link>
        <Link className="btn btn-success" to="/unitCreate">Skapa Unit</Link>
        <Link className="btn btn-danger" to="/unitShow">Visa Units</Link>
        <Link className="btn btn-danger" to="/showWorkers">Visa alla Workers</Link>
        <Link className="btn btn-success" to="/createUnitTask">Skapa UnitTask</Link>
        <Link className="btn btn-info" to="/showUnitTasks">Visa UnitTasks</Link>
        <hr />
        <Link className="btn btn-primary" to="/workerRegister">Registrera en worker</Link>
    </>
    :
       <></>
    }
    </> );
}

export default Home;
