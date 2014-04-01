import socket, csv, sys
from multiprocessing import pool

SOCKET_TIMEOUT = 10

def IsPortOpen( (SomeHost, SomePort) ):
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.settimeout(SOCKET_TIMEOUT)
    try:
        s.connect( (SomeHost, SomePort) )
        return 1
    except:
        return 0

def ArePortsOpen(SomeList):
    p = pool.ThreadPool(len(SomeList))
    return p.map(IsPortOpen, SomeList)

def IsValidRow(SomeRow):
    '''Row should be in the format:
    	ip,
	port,
	descriptive name,
	is_site_up (1 or 0), 
	number_of_times_up (int), 
	number_of_times_checked (int), 
	service type, 
	karma sort,
	network id,
	hash (for checking owner/creator)
    '''
    try:
        #Check to see if it has the right number of fields
        if(len(SomeRow) != 10):
            return 0
        #Make sure int fields are ints
        int(SomeRow[1])
        int(SomeRow[3])
        int(SomeRow[4])
        int(SomeRow[5])
	int(SomeRow[7])


        ''' We could also do something like this:
        #Check to see if we're above maximum number of failures
        if(int(SomeRow[4]) >= MAX_FAILURES):
            return 0
        '''
        #Row is good
        return 1
    except:
        #If an exception occurred, something is wrong with this row.
        return 0

def main(InFile, OutFile):
    with open(InFile, 'r') as infile:
        reader = csv.reader(infile, delimiter = '|')
        rows = [x for x in reader]
        rows = filter(IsValidRow, rows)
    results = ArePortsOpen([(row[0], int(row[1])) for row in rows])
    with open(OutFile, 'w') as outfile:
        writer = csv.writer(outfile, delimiter = '|')
        for row, status in zip(rows, results):
            if status:
                writer.writerow([row[0], row[1], row[2], status, int(row[4]) + 1, int(row[5]) + 1, row[6], row[7], row[8], row[9].strip()])
            else:
                writer.writerow([row[0], row[1], row[2], status, int(row[4]), int(row[5]) + 1, row[6], row[7], row[8], row[9].strip()])
    return 0


if __name__ == "__main__":
    try:
        main(sys.argv[1], sys.argv[2])
    except Exception as e:
        print 'Error:', e 
        print 'Usage: python serv.py infile outfile'
