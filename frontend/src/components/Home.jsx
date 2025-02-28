import React, {useContext} from 'react';
import { useEffect } from 'react';

import { LoadingContext, AuthContext } from '../App';
import { Link, useNavigate } from 'react-router-dom';

function Home() {
    const {isAuth, userId, setIsAuth, setUserId, userRole, setUserRole, userName, setUserName} = useContext(AuthContext);
    const navigate = useNavigate();
    /* console.log("before");
    console.log(userName); */

   /*  useEffect(() => {
        if (!isAuth) {
            navigate('/');
        }
    }, []); */ //HM checksession. DET ÄR FEL MED SESSION. I LOGIN SÅ REDIRECTAR JAG INNAN OMRENDERING EFTER SET FUNKTION PÅ USERID... HUR SKA JAG KONTROLLERA OM DET FINNS SESSION AKTIV PÅ PROTECTED ROUTES..
    async function logout(){
        try{
            let res = await fetch('http://localhost/mini-axami/public/api/logout',{
                method:'POST',
                headers: { 'Content-Type': 'application/json' },
            }
            );
    
            if(!res.ok){
                console.log('fetch failed');
            }
    
            let jsonRes = await res.json();
    
            if(jsonRes.success){
                console.log(jsonRes.success);
                setIsAuth(false);
                navigate('/');
            }else{
                console.log(jsonRes.error);
            }
    
            
        }catch(error){
            console.log('Triggered catch', error);
        }
    }

    const handleProfilePage = () => {
        navigate('/customerProfile/'+userId);
    };
    return ( <>
    {isAuth ? 
    <>
        <h1 className='text-center text-primary'>Välkommen till Mini axami Underhållssystem</h1>
        <h3>Your name is <strong>{userName}</strong></h3>
        <h3>Your id is <strong>{userId}</strong></h3>
        <h3>Your role is <strong>{userRole}</strong></h3>

        <button className="btn btn-info" onClick={handleProfilePage}>ProfilSida</button>
        <Link className="btn btn-success" to="/unitCreate">Skapa Unit</Link>
        <Link className="btn btn-danger" to="/unitShow">Visa Units</Link>
        <Link className="btn btn-danger" to="/showWorkers">Visa alla Workers</Link>
        <button onClick={logout} className='btn bg-danger text-light'>Logout</button>
        <hr />
        <Link className="btn btn-primary" to="/workerRegister">Registrera en worker</Link>
    </>
    :
       <></>
    }
    </> );
}

export default Home;
