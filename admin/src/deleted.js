import React from 'react';
import {
	List, 
	Show, 
	Create,
	Datagrid, 
	ReferenceField, 
	TextField,
	DateField,
	DeleteButton,
	ShowButton,
	DisabledInput, 
	LongTextInput, 
	ReferenceInput, 
	SelectInput, 
	SimpleShowLayout, 
	TextInput,
	DateInput,
	RadioButtonGroupInput} from 'admin-on-rest';

export const DeletedList = (props) => (
    <List {...props}>
		<Datagrid>
			<TextField source="id" />
			<ReferenceField 
				label="Category" 
				source="categoryId" 
				reference="categories">
				<TextField source="title" />
			</ReferenceField>
			<DateField source="created" />	
			<TextField source="title" />
			<ShowButton />
			<DeleteButton />
		</Datagrid>
    </List>
);

const DeletedTitle = ({ record }) => {
    return <span>Post {record ? `"${record.title}"` : ''}</span>;
};

export const DeletedShow = (props) => (
    <Show title={<DeletedTitle />} {...props}>
        <SimpleShowLayout>
            <TextField source="id" />
			<DateField
				label="Publication date" 
				source="created"
				locales="ru-RU"		
				showTime
			/>
			<ReferenceField
				label="Category" 
				source="categoryId" 
				reference="categories">
				<TextField source="title" />
			</ReferenceField>
            <TextField source="title" />
			<TextField source="text" />
        </SimpleShowLayout>
    </Show>
);