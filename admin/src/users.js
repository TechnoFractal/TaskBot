import React from 'react';
import {
	List, 
	Datagrid, 
	TextField,
	DeleteButton} from 'admin-on-rest';

export const UserList = (props) => (
    <List {...props}>
		<Datagrid>
			<TextField source="id" />
			<TextField source="login" />
			<DeleteButton />
		</Datagrid>
    </List>
);

const UserTitle = ({ record }) => {
    return <span>User {record ? `"${record.login}"` : ''}</span>;
};