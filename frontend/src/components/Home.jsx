import React, {useContext} from 'react';
import { useEffect } from 'react';

import { LoadingContext, AuthContext } from '../App';
import { Link, useNavigate } from 'react-router-dom';

function Home() {
    const {isAuth, userId, setUserId, userRole, setUserRole, userName, setUserName} = useContext(AuthContext);
    /* const navigate = useNavigate(); */
    /* console.log("before");
    console.log(userName); */

   /*  useEffect(() => {
        if (!isAuth) {
            navigate('/');
        }
    }, []); */ //HM checksession. DET ÄR FEL MED SESSION. I LOGIN SÅ REDIRECTAR JAG INNAN OMRENDERING EFTER SET FUNKTION PÅ USERID... HUR SKA JAG KONTROLLERA OM DET FINNS SESSION AKTIV PÅ PROTECTED ROUTES..

    return ( <>
    {isAuth ? 
    <>
        <h1 className='text-center text-primary'>Välkommen till Mini axami Underhållssystem</h1>
        <h3>Your name is <strong>{userName}</strong></h3>
        <h3>Your id is <strong>{userId}</strong></h3>
        <h3>Your role is <strong>{userRole}</strong></h3>

        <Link className="btn btn-danger" to="/unitCreate">Skapa Unit</Link>
        <Link className="btn btn-danger" to="/unitShow">Visa Units</Link>
    </>
    :
       <></>
    }
    </> );
}

export default Home;
